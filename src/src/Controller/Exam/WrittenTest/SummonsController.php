<?php

declare(strict_types=1);

namespace App\Controller\Exam\WrittenTest;

use App\Entity\Exam\ExamClassification;
use App\Entity\User;
use App\Message\WrittenTest\SummonsMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/admin/exam/written_test/summons')]
#[IsGranted('ROLE_RESPONSABLE')]
class SummonsController extends AbstractController
{
    #[Route('/{id}/send_summons', name: 'written_test_summons', methods: ['GET'])]
    public function sendSummons(
        ExamClassification $examClassification,
        MessageBusInterface $bus,
        Security $security,
    ): Response
    {
        /** @var User $user */
        $user = $security->getUser();

        $bus->dispatch(new SummonsMessage($examClassification->getId(), $user->getId()));

        $this->addFlash('sucess', 'La génération des convocations est en cours. Vous recevrez une notification lorsque la génération sera terminée');

        return $this->redirectToRoute('exam_session_convocation', []);
    }
}