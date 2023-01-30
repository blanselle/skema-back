<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Entity\Exam\ExamRoom;
use App\Entity\Exam\ExamSession;
use App\Form\Exam\ExamSessionRoomType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/exams/sessions')]
#[IsGranted('ROLE_COORDINATOR')]
class ExamRoomController extends AbstractController
{
    #[Route('/{id}/edit/rooms', name: 'exam_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExamSession $examSession, EntityManagerInterface $entityManager): Response
    {
        $examForm = clone $examSession;
        $examFormRoom = new ArrayCollection();
        $examFormRoom->add(new ExamRoom());
        $examForm->setExamRooms($examFormRoom);
        $form = $this->createForm(ExamSessionRoomType::class, $examForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (count($examFormRoom) > 0 && isset($examFormRoom[0])) {
                $entityManager->persist($examFormRoom[0]);
                $examSession->addExamRoom($examFormRoom[0]);
            }
            $entityManager->flush();

            return $this->redirectToRoute('exam_room_edit', ['id' => $examSession->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/room/edit.html.twig', [
            'item' => $examSession,
            'form' => $form,
        ]);
    }

    #[Route('/{examSession}/rooms/{examRoom}', name: 'exam_room_delete', methods: ['POST'])]
    public function delete(Request $request, ExamSession $examSession, ExamRoom $examRoom, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examRoom->getId(), strval($request->request->get('_token')))) {
            $examSession->removeExamRoom($examRoom);
            $entityManager->flush();
        }

        return $this->redirectToRoute('exam_room_edit', ['id' => $examSession->getId()], Response::HTTP_SEE_OTHER);
    }
}
