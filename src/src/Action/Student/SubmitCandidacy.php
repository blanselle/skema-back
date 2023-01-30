<?php

declare(strict_types=1);

namespace App\Action\Student;

use App\Entity\Student;
use App\Service\Bloc\BlocRewriter;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SubmitCandidacy extends AbstractController
{
    public function __invoke(
        Student $student,
        StudentWorkflowManager $studentWorkflowManager,
        BlocRewriter $blocRewriter,

    ): Response {
        
        if($studentWorkflowManager->eligibleToComplete($student)) {
            return new JsonResponse([
                'message' => 'ok',
            ]);
        }

        return $this->json([
            "message" => $blocRewriter->rewriteBloc('ERROR_CANDIDACY_SUBMISSION_POPIN', $student->getProgramChannel())->getContent(),
        ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
