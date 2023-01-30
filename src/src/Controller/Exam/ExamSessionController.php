<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Form\Admin\User\AdministrativeRecord\ExamStudentType;
use App\Form\Exam\ExamSessionType;
use App\Repository\CampusRepository;
use App\Repository\Payment\OrderRepository;
use App\Service\Datatable;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/exams/sessions')]
#[IsGranted('ROLE_COORDINATOR')]
class ExamSessionController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('', name: 'exam_session_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Datatable $datatable, CampusRepository $campusRepository): Response
    {
        $campuses = $campusRepository->findBy(['assignmentCampus' => true], ['name' => 'asc']);
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'name'     => $datatable->filter('name'),
            'campus'   => $datatable->filter('campus'),
        ]);

        $data['columns'] = [
            'id'            => ['db' => 'a.id', 'label' => "Identifiant"],
            'name'          => ['db' => 'cl.name', 'label' => "Nom"],
            'dateStart'     => ['db' => 'a.dateStart', 'label' => "Date début"],
            'campus'        => ['db' => 'c.name', 'label' => "Campus"],
            'numberOfPlace' => ['db' => 'a.numberOfPlaces', 'label' => "Places"],
            'price'         => ['db' => 'a.price', 'label' => "Prix"],
            'action'        => ['label' => "Actions"],
        ];
        return $datatable->getDatatableResponse($request, ExamSession::class, $data, 'exam/session', [
            'campuses' => $campuses,
        ]);
    }

    #[Route('/new', name: 'exam_session_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $examSession = new ExamSession();
        $form = $this->createForm(ExamSessionType::class, $examSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($examSession);
            $this->em->flush();

            return $this->redirectToRoute('exam_session_edit', ['id' => $examSession->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/session/new.html.twig', [
            'exam' => $examSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'exam_session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExamSession $examSession): Response
    {
        $form = $this->createForm(ExamSessionType::class, $examSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('exam_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/session/edit.html.twig', [
            'item' => $examSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'exam_session_delete', methods: ['POST'])]
    public function delete(Request $request, ExamSession $examSession, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examSession->getId(), strval($request->request->get('_token')))) {
            $hasOrders = (count($orderRepository->findBy(['examSession' => $examSession])) > 0);
            if ($hasOrders) {
                $this->addFlash('error', 'Des paiements sont en cours sur cette session.');
            }

            if (!$hasOrders) {
                try {
                    $this->em->remove($examSession);
                    $this->em->flush();
                } catch (ForeignKeyConstraintViolationException $e) {
                    $this->addFlash('error', 'Des candidats sont inscrits à cette session, vous ne pouvez pas la supprimer');
                }
            }
        }

        return $this->redirectToRoute('exam_session_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/room', name: 'exam_session_room', methods: ['GET'])]
    public function createSelectRoom(ExamSession $examSession): Response
    {
        $examStudent = (new ExamStudent())->setExamSession($examSession);
        $form = $this->createForm(ExamStudentType::class, $examStudent);
        return $this->renderForm('exam/examStudent/_select_room.html.twig', parameters: ['form' => $form]);
    }
}
