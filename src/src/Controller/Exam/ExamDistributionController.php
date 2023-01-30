<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Campus;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Form\Exam\ExamStudentRoomType;
use App\Service\Exam\DistributionManager;
use App\Service\Exam\SessionExport;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

#[Route('/admin/exams/sessions/distribution')]
#[IsGranted('ROLE_COORDINATOR')]
class ExamDistributionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private DistributionManager $distributionManager
    ) {
    }

    #[Route('', name: 'exam_distribution_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $campuses = $this->em->getRepository(Campus::class)->findBy([], ['name' => 'asc']);

        if ($request->isMethod('POST')) {
            try {
                $this->distributionManager->makeDistribution((int)$request->request->get('campus'));
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        $campusesDistributed = $this->em->getRepository(ExamSession::class)->getCampusesWithExamSessionDistributed();

        return $this->renderForm('exam/distribution/index.html.twig', [
            'campuses' => $campuses,
            'campusesDistributed' => $campusesDistributed,
        ]);
    }

    #[Route('/{campus}/edit', name: 'exam_distribution_list', methods: ['GET'])]
    public function list(Campus $campus): Response
    {
        $display = [];
        $exams = $this->em->getRepository(ExamSession::class)->findBy(['campus' => $campus, 'type' => ExamSessionTypeConstants::TYPE_INSIDE]);

        foreach ($exams as $exam) {
            /** @var ExamSession $exam */
            $display[$exam->getExamClassification()->getName()][] = [
                'id' => $exam->getId(),
                'name' => $exam->getExamClassification()->getName(),
                'date' => $exam->getDateStart()->format('Y-m-d H:i'),
                'students' => $this->em->getRepository(ExamStudent::class)->getExamStudentsByExamSession(examSession: $exam, allStudent: true),
                'exportFileName' => sprintf(
                    'repartition-%s-.xlsx',
                    (new UnicodeString($exam->getExamClassification()->getName() . '-' . $campus->getName()))
                )
            ];
        }

        return $this->renderForm('exam/distribution/list.html.twig', [
            'exams' => $display,
            'campus' => $campus
        ]);
    }

    #[Route('/{campus}/edit/{id}', name: 'exam_distribution_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campus $campus, ExamStudent $examStudent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExamStudentRoomType::class, $examStudent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('exam_distribution_list', ['campus' => $campus->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/distribution/edit.html.twig', [
            'form' => $form,
            'campus' => $campus,
            'student' => $examStudent,
        ]);
    }

    #[Route('/{campus}/{id}/edit', name: 'exam_distribution_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Campus $campus, ExamStudent $examStudent): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examStudent->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($examStudent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('exam_distribution_list', ['campus' => $campus->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/export', name: 'exam_session_export')]
    public function export(
        ExamSession $examSession,
        SessionExport $sessionExport,
    ): Response {
        $tempFile = $sessionExport->export($examSession);
        
        return $this->file($tempFile, sprintf('session-export-%s.xlsx', $examSession->getId()), ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
