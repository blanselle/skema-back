<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Repository\Exam\ExamClassificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/exams/sessions/convocations')]
#[IsGranted('ROLE_COORDINATOR')]
class ExamSessionConvocationController extends AbstractController
{
    #[Route('', name: 'exam_session_convocation', methods: ['GET'])]
    public function index(ExamClassificationRepository $examClassificationRepository): Response
    {
        $examClassifications = $examClassificationRepository->findBy([], ['name' => 'ASC']);

        return $this->renderForm('exam/session/convocation/list.html.twig', [
            'examClassifications' => $examClassifications,
        ]);
    }
}
