<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use App\Constants\User\StudentWorkflowStateConstants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/admin/students')]
class StudentActivationController extends AbstractController
{
    #[Route('/{user}/activate', name: 'student_account_activate', methods: ['GET'])]
    public function sendMailStudent(
        User $user, 
        StudentWorkflowManager $studentWorkflowManager,
    ): RedirectResponse
    {
        if($user->getStudent()->getState() !== StudentWorkflowStateConstants::STATE_START) {
            throw new AccessDeniedHttpException('This account is already active');
        }
        $studentWorkflowManager->activeAccount($user->getStudent());

        $this->addFlash('success', 'L\'adresse email est validé');
        return $this->redirectToRoute('student_edit', ['id' => $user->getStudent()->getId()]);
    }

}
