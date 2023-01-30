<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Constants\User\StudentConstants;
use App\Entity\Student;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/admin/students')]
#[IsGranted('ROLE_COORDINATOR')]
class CheckDiplomaValidationController extends AbstractController
{
    #[Route('/{id}/check_diploma', name: 'check_diploma_validation', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Student $student,
        StudentWorkflowManager $studentWorkflowManager,
        UrlGeneratorInterface $urlGenerator,
    ): Response {
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token');
            $submittedValidation = $request->request->get('check_diploma');
            if ($this->isCsrfTokenValid('student'.$student->getId(), strval($submittedToken))) {
                if (null != $submittedValidation) {
                    switch ($submittedValidation) {
                        case StudentConstants::VALUE_VALIDATE:
                            $studentWorkflowManager->acceptCheckDiploma($student);
                            break;
                        case StudentConstants::VALUE_REJECTED:
                            $studentWorkflowManager->rejectCheckDiploma($student);
                            break;
                    }
                }
            }
        }

        return new RedirectResponse($urlGenerator->generate('student_edit', ['id' => $student->getId()]));
    }
}
