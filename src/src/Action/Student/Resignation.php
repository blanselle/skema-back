<?php

declare(strict_types=1);

namespace App\Action\Student;

use App\Constants\User\ResignationConstants;
use App\Entity\Student;
use App\Exception\Bloc\BlocNotFoundException;
use App\Repository\BlocRepository;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Resignation extends AbstractAuthenticatedStudentController
{
    public function __invoke(
        Student $student,
        StudentWorkflowManager $studentWorkflowManager,
        BlocRepository $blocRepository,
    ): Response {

        $this->checkAuthenticatedStudent($student);
        
        $result = $studentWorkflowManager->resignation($student);

        $bloc = $blocRepository->findActiveByKey(ResignationConstants::RESIGNATION_LABEL_MESSAGE);

        if (null === $bloc) {
            throw new BlocNotFoundException('RESIGNATION_LABEL_MESSAGE');
        }

        if (true === $result) {
            return new JsonResponse([
                'status' => 201,
                'message' => $bloc->getContent(),
            ]);
        }

        throw new BadRequestException('Error, you can\'t resign');
    }
}
