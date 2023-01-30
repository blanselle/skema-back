<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamClassificationScore;
use App\Form\Exam\ExamClassificationScoreType;
use App\Manager\ExamClassificationScoreManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

//#[Route('/admin/exams/sessions/classification')]
#[IsGranted('ROLE_COORDINATOR')]
class ExamClassificationScoreController extends AbstractController
{
    #[Route('/admin/exams/sessions/classification/{id}/scores', name: 'exam_classification_scores_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ExamClassification $examClassification, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        return $this->render('exam/classification/score/index.html.twig', [
            'examClassificationScores' => $em->getRepository(ExamClassificationScore::class)->findBy(['examClassification' => $examClassification], ['score' => 'ASC']),
            'examClassification' => $examClassification
        ]);
    }

    #[Route('/admin/exams/sessions/classification/{id}/scores/new', name: 'exam_classifications_score_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ExamClassification $examClassification, EntityManagerInterface $em): Response
    {
        $examClassificationScore = new ExamClassificationScore();
        $examClassificationScore->setExamClassification($examClassification);
        $form = $this->createForm(ExamClassificationScoreType::class, $examClassificationScore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($examClassificationScore);
            $em->flush();

            $this->addFlash(
                'sucess',
                'Le nouveau score ont bien été créé'
            );

            return $this->render('exam/classification/score/index.html.twig', [
                'examClassificationScores' => $em->getRepository(ExamClassificationScore::class)->findBy(['examClassification' => $examClassification], ['score' => 'ASC']),
                'examClassification' => $examClassification
            ]);
        }

        return $this->renderForm('exam/classification/score/new.html.twig', [
            'examClassification' => $examClassification,
            'form' => $form,
        ]);
    }

    #[Route('/admin/exams/sessions/classification/{id}/scores/upload', name: 'exam_classifications_score_upload', methods: ['GET', 'POST'])]
    public function upload(
        Request $request,
        ExamClassification $examClassification,
        EntityManagerInterface $em,
        ExamClassificationScoreManager $examClassificationScoreManager
    ): Response {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'Fichier CSV',
                'help' => 'Le fichier doit être au format csv et ne contenir qu\'une colonne. Chaque ligne ne doit contenir qu\'un score.'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $newScores = [];
            $handle = fopen($file->getPathname(), "r");
            if ($handle !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    $newScores[] = floatval($data[0]);
                }
                fclose($handle);
            }

            if (empty($newScores)) {
                $this->addFlash(
                    'error',
                    'Les données du fichiers ne sont pas correctement formatées'
                );
            } else {
                $examClassificationScoreManager->importNewScores($em, $examClassification, $newScores);

                $this->addFlash(
                    'sucess',
                    'Les nouveaux scores ont bien été importés'
                );
            }

            return $this->render('exam/classification/score/index.html.twig', [
                'examClassificationScores' => $em->getRepository(ExamClassificationScore::class)->findBy(['examClassification' => $examClassification], ['score' => 'ASC']),
                'examClassification' => $examClassification
            ]);
        }

        return $this->renderForm('exam/classification/score/upload.html.twig', [
            'examClassification' => $examClassification,
            'form' => $form,
        ]);
    }

    #[Route('/admin/exams_classification_scores/{id}', name: 'exam_classifications_score_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExamClassificationScore $examClassificationScore, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ExamClassificationScoreType::class, $examClassificationScore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'sucess',
                'Le score ont bien été modifié'
            );

            return $this->redirectToRoute(
                'exam_classification_scores_index',
                ['id' => $examClassificationScore->getExamClassification()->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('exam/classification/score/edit.html.twig', [
            'examClassificationScore' => $examClassificationScore,
            'form' => $form,
        ]);
    }

    #[Route('/admin/exams_classification_scores/{id}/delete', name: 'exam_classification_scores_delete', methods: ['POST'])]
    public function delete(Request $request, ExamClassificationScore $examClassificationScore, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examClassificationScore->getId(), strval($request->request->get('_token')))) {
            $em->remove($examClassificationScore);
            $em->flush();

            $this->addFlash(
                'sucess',
                'Le nouveau score ont bien été supprimé'
            );
        }

        return $this->redirectToRoute(
            'exam_classification_scores_index',
            ['id' => $examClassificationScore->getExamClassification()->getId()],
            Response::HTTP_SEE_OTHER
        );
    }
}
