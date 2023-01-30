<?php

declare(strict_types=1);

namespace App\Action\AdministrativeRecord;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Exception\ValidationException;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Service\Bloc\BlocRewriter;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArAssertsValidationController extends AbstractController
{
    public function __invoke(
        AdministrativeRecord $administrativeRecord,
        ValidatorInterface $validator,
        StudentWorkflowManager $studentWorkflowManager,
        BlocRewriter $blocRewriter,
    ): Response {
        $errors = $validator->validate($administrativeRecord, groups: 'ar:validated');

        if (0 !== count($errors)) {
            return new JsonResponse(
                (new ValidationException($errors))->getMessages(),
                Response::HTTP_BAD_REQUEST
            );
        }

        $oldState = $administrativeRecord->getStudent()->getState();

        if ($oldState !== StudentWorkflowStateConstants::STATE_CREATED) {
            
            if($oldState === StudentWorkflowStateConstants::STATE_CHECK_DIPLOMA) {
                $blocKey = 'ADMINISTRATIVE_RECORD_ERROR_MESSAGE_CHECK_DIPLOMA';
            } else {
                $blocKey = 'ADMINISTRATIVE_RECORD_ERROR_MESSAGE';
            }

            return new JsonResponse(
                [
                    "completed" => false,
                    "message" => $blocRewriter->rewriteBloc($blocKey, $administrativeRecord->getStudent()->getProgramChannel())->getContent(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $stateWasChanged = false;
        
        if(true === $studentWorkflowManager->arToCheck($administrativeRecord->getStudent())) {
            $stateWasChanged = true;
        }

        if(true === $studentWorkflowManager->arValidated($administrativeRecord->getStudent())) {
            $stateWasChanged = true;
        }

        return new JsonResponse(
            [
                "completed" => $stateWasChanged,
            ]
        );
    }
}
