<?php

declare(strict_types=1);

namespace App\Action\Student;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Exception\Bloc\BlocNotFoundException;
use App\Service\Bloc\BlocRewriter;
use App\Service\User\TokenManager;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccountActivationController extends AbstractController
{
    private const STUDENT_STATE_TO_BLOC_KEY = [
        StudentWorkflowStateConstants::STATE_EXEMPTION       => 'ERROR_CANDIDATE_EXEMPTION',
        StudentWorkflowStateConstants::STATE_CHECK_DIPLOMA   => 'USER_CREATION_OK',
        StudentWorkflowStateConstants::STATE_CREATED         => 'USER_CREATION_OK',
    ];

    public function __invoke(
        Request $request,
        TokenManager $tokenManager,
        StudentWorkflowManager $studentWorkflowManager,
        EntityManagerInterface $em,
        BlocRewriter $blocRewriter,
    ): Response {
        
        $params = $request->toArray();
        if(!isset($params['token'])) {
            throw new BadRequestException('The token is missing');
        }

        try {
            $user = $tokenManager->parse($params['token']);
        } catch(JWTDecodeFailureException $e) {
            
            $bloc = $blocRewriter->rewriteBloc('EXPIRED_TOKEN_MESSAGE');
        
            return $this->json([
                'message' => $bloc->getContent(),
            ]);

        } catch(Exception $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $student = $user->getStudent();
        if(null === $student) {
            throw new Exception('The user does not have student');
        }
        
        if($user->getStudent()->getState() !== StudentWorkflowStateConstants::STATE_START) {
            throw new AccessDeniedHttpException('This account is already active');
        }

        $studentWorkflowManager->activeAccount($student);

        $em->flush();

        $state = $student->getState();
        if(!isset(self::STUDENT_STATE_TO_BLOC_KEY[$state])) {
            throw new BlocNotFoundException($state);
        }
        
        $bloc = $blocRewriter->rewriteBloc(self::STUDENT_STATE_TO_BLOC_KEY[$state]);
        
        return $this->json([
            'message' => $bloc->getContent(),
        ]);
    }
}
