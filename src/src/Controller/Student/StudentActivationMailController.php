<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Mail\AccountActivationMailDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/students')]
class StudentActivationMailController extends AbstractController
{
    #[Route('/{user}/mailsend', name: 'exam_student_mail_send', methods: ['GET'])]
    public function sendMailStudent(
        User $user, AccountActivationMailDispatcher $mailer
    ): RedirectResponse
    {
        $mailer->dispatch($user->getStudent());
        $this->addFlash('success', 'Le mail sera envoyé d\'ici quelques secondes ');
        return $this->redirectToRoute('student_edit', ['id' => $user->getStudent()->getId()]);
    }

}
