<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Entity\Exam\ExamClassification;
use App\Form\Exam\ExamClassificationType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/exams/sessions/classification')]
#[IsGranted('ROLE_RESPONSABLE')]
class ExamClassificationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'exam_classifications_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $this->em->getRepository(ExamClassification::class)->findBy([], ['name' => 'ASC']),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('exam/classification/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'exam_classifications_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $examClassification = new ExamClassification();
        $form = $this->createForm(ExamClassificationType::class, $examClassification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($examClassification);
            $this->em->flush();

            return $this->redirectToRoute('exam_classifications_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/classification/new.html.twig', [
            'exam' => $examClassification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'exam_classifications_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExamClassification $examClassification): Response
    {
        $form = $this->createForm(ExamClassificationType::class, $examClassification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('exam_classifications_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/classification/edit.html.twig', [
            'exam' => $examClassification,
            'form' => $form,
            'summonsAlreadySended' => ($request->query->get('summons_already_sended'))
        ]);
    }

    #[Route('/{id}', name: 'exam_classifications_delete', methods: ['POST'])]
    public function delete(Request $request, ExamClassification $examClassification): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examClassification->getId(), strval($request->request->get('_token')))) {
            $this->em->remove($examClassification);
            $this->em->flush();
        }

        return $this->redirectToRoute('exam_classifications_index', [], Response::HTTP_SEE_OTHER);
    }
}