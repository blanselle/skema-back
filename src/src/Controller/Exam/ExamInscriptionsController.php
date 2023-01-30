<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Entity\Campus;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Form\Exam\ExamClassificationExportOnlineType;
use App\Service\Datatable;
use App\Service\Exam\SessionExport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/exams/sessions/inscription')]
#[IsGranted('ROLE_COORDINATOR')]
class ExamInscriptionsController extends AbstractController
{
    #[Route('', name: 'exam_inscriptions_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, Datatable $datatable, SessionExport $sessionExport, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ExamClassificationExportOnlineType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tempFile = $sessionExport->exportOnline($form->get('examSession')->getData(), $form->get('delay')->getData());

            if (!empty($form->get('examSession')->getData())) {
                $examName = $slugger->slug(strtolower($form->get('examSession')->getData()->getExamClassification()->getName()));
                return $this->file($tempFile, sprintf('inscription-en-ligne-export-%s.xlsx', $examName), ResponseHeaderBag::DISPOSITION_INLINE);
            }

            return $this->file($tempFile, 'session-export-all.xlsx', ResponseHeaderBag::DISPOSITION_INLINE);
        }

        $typologies = $em->getRepository(ExamClassification::class)->findBy([], ['name' => 'asc']);
        $examsOnline = $em->getRepository(ExamSession::class)->getExamSessionsOnline(false);
        $idCampus = $request->query->get('id_campus');
        $campusQuery = $request->query->get('campus');
        $campuses = $em->getRepository(Campus::class)->findBy(['assignmentCampus' => true], ['name' => 'asc']);
        if (empty($idCampus) && 'online' !== $campusQuery && count($campuses) > 0) {
            $campus = $campuses[0];
            $idCampus = $campus->getId();
        }
        $online = $request->query->getBoolean('online') || 'online' === $campusQuery;

        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'candidate' => $datatable->filter('candidate'),
            'lastname'  => $datatable->filter('lastname'),
            'exam'      => $datatable->filter('exam'),
            'campus'    => $datatable->filter('campus'),
        ]);

        $data['columns'] = [
            'identifier'    => ['db' => 'st.identifier', 'label' => "Candidat"],
            'firstName'     => ['db' => 'u.firstName', 'label' => "Prénom"],
            'lastName'      => ['db' => 'u.lastName', 'label' => "Nom"],
            'name'          => ['db' => 'cl.name', 'label' => "Épreuve"],
            'dateStart'     => ['db' => 'e.dateStart', 'label' => "Date début"],
            'campus'        => ['db' => 'c.name', 'label' => "Campus"],
            'action'        => ['label' => "Actions"],
        ];

        return $datatable->getDatatableResponse($request, ExamStudent::class, $data, 'exam/inscription', [
            'campuses' => $campuses,
            'id_campus' => $idCampus,
            'examsOnline' => $examsOnline,
            'online' => $online,
            'typologies' => $typologies,
            'form' => $form->createView()
        ]);
    }

    #[Route('/student/{examStudent}', name: 'exam_inscription_delete', methods: ['POST'])]
    public function delete(Request $request, ExamStudent $examStudent, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examStudent->getId(), strval($request->request->get('_token')))) {
            $em->remove($examStudent);
            $em->flush();
        }

        return $this->redirectToRoute('exam_inscriptions_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/student/{examStudent}/edit', name: 'exam_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExamStudent $examStudent, EntityManagerInterface $em): Response
    {
        $exams = $em->getRepository(ExamStudent::class)->getExamStudentsForActiveSessions(['identifier' => $examStudent->getStudent()->getIdentifier()]);
        $examsOnline = $em->getRepository(ExamSession::class)->getExamSessionsOnline();
        $campuses = $em->getRepository(Campus::class)->getExamSessionsActiveByCampus();

        return $this->renderForm('exam/inscription/edit.html.twig', [
            'exams' => $exams,
            'item' => $examStudent,
            'examsOnline' => $examsOnline,
            'campuses' => $campuses
        ]);
    }

    #[Route('/session/{examSession}', name: 'exam_inscription_session', methods: ['GET'])]
    public function detailsSessionWithRooms(Request $request, ExamSession $examSession): Response
    {
        return $this->renderForm('exam/inscription/rooms.html.twig', [
            'item' => $examSession
        ]);
    }
}
