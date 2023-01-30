<?php

declare(strict_types=1);

namespace App\Controller\Admissibility;

use App\Entity\Student;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Message\NotificationsAdmissibilityResultMessage;
use App\Repository\Admissibility\LandingPage\AdmissibilityStudentTokenRepository;
use App\Service\Notification\NotificationAdmissibilityResultDispatcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;

#[Route('/admin/students/notifications-admissibility')]
#[IsGranted('send-admissibility')]
class NotificationsAdmissibilityResultController extends AbstractController
{
    #[Route('/', name: 'notifications_admissibility', methods: ['GET'])]
    public function index(
        MessageBusInterface $dispatcher,
        Security $security,
    ): Response {
        $dispatcher->dispatch((new NotificationsAdmissibilityResultMessage())
            ->setReceiverIdentifier($security->getUser()->getUserIdentifier())
        );

        $this->addFlash('success', 'Envoie des mails d\'admissibilités');

        return $this->redirectToRoute('student_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'notification_admissibility', methods: ['GET', 'POST'])]
    public function item(
        #[Autowire('%admissibility_landing_url%')]
        string $admissibilityLandingUrl,
        Request $request,
        Student $student,
        NotificationAdmissibilityResultDispatcher $dispatcher,
        AdmissibilityStudentTokenRepository $admissibilityStudentTokenRepository,
    ): Response {
        if($request->getMethod() === 'POST') {
 
            $dispatcher->dispatch($student);
            
            $this->addFlash('success', 'Envoi du mail d\'admissibilité');
            
            return $this->redirectToRoute('student_edit', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
        }
        
        $admissibilityStudentToken = $admissibilityStudentTokenRepository->findOneBy(['student' => $student]);
        return $this->render('admissibility/notification_admissibility_result.html.twig', [
            'admissibilityResultUrl' => sprintf(
                '%s?token=%s', 
                $admissibilityLandingUrl,
                $admissibilityStudentToken->getToken(),
            ),
            'student' => $student,
        ]);
    }
}
