<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Entity\Exam\ExamLanguage;
use App\Form\Exam\ExamLanguageType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/exam/languages')]
#[IsGranted('ROLE_RESPONSABLE')]
class ExamLanguageController extends AbstractController
{
    #[Route('', name: 'exam_language_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        return $this->render('exam/language/index.html.twig', [
            'examLanguages' => $em->getRepository(ExamLanguage::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'exam_language_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $examLanguage = new ExamLanguage();
        $form = $this->createForm(ExamLanguageType::class, $examLanguage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($examLanguage);
            $em->flush();

            return $this->redirectToRoute('exam_language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/language/new.html.twig', [
            'language' => $examLanguage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'exam_language_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExamLanguage $examLanguage, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ExamLanguageType::class, $examLanguage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('exam_language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/language/edit.html.twig', [
            'language' => $examLanguage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'exam_language_delete', methods: ['POST'])]
    public function delete(Request $request, ExamLanguage $examLanguage, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examLanguage->getId(), strval($request->request->get('_token')))) {
            $em->remove($examLanguage);
            $em->flush();
        }

        return $this->redirectToRoute('exam_language_index', [], Response::HTTP_SEE_OTHER);
    }
}
