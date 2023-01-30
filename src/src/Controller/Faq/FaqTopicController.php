<?php

declare(strict_types=1);

namespace App\Controller\Faq;

use App\Entity\Faq\FaqTopic;
use App\Form\Faq\FaqTopicType;
use App\Repository\Faq\FaqTopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/faqs/topic')]
#[IsGranted('ROLE_COORDINATOR')]
class FaqTopicController extends AbstractController
{
    #[Route('', name: 'faq_topic_index', methods: ['GET'])]
    public function index(Request $request, FaqTopicRepository $faqTopicRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $faqTopicRepository->findBy([], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('faq/topic/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'faq_topic_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exam = new FaqTopic();
        $form = $this->createForm(FaqTopicType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exam);
            $entityManager->flush();

            return $this->redirectToRoute('faq_topic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('faq/topic/new.html.twig', [
            'topic' => $exam,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'faq_topic_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FaqTopic $faqTopic, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FaqTopicType::class, $faqTopic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('faq_topic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('faq/topic/edit.html.twig', [
            'topic' => $faqTopic,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'faq_topic_delete', methods: ['POST'])]
    public function delete(Request $request, FaqTopic $faqTopic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$faqTopic->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($faqTopic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('faq_topic_index', [], Response::HTTP_SEE_OTHER);
    }
}
