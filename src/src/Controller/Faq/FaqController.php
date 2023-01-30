<?php

declare(strict_types=1);

namespace App\Controller\Faq;

use App\Entity\Faq\Faq;
use App\Entity\Faq\FaqTopic;
use App\Form\Faq\FaqType;
use App\Service\Datatable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/faqs')]
#[IsGranted('ROLE_COORDINATOR')]
class FaqController extends AbstractController
{
    #[Route('', name: 'faq_faq_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, Datatable $datatable): Response
    {
        $topics = $em->getRepository(FaqTopic::class)->findBy([], ['label' => 'asc']);

        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'question'     => is_string($datatable->filter('question')) && !empty($datatable->filter('question'))
                ? html_entity_decode($datatable->filter('question')) : null,
            'topic'     => $datatable->filter('topic'),
        ]);

        $data['columns'] = [
            'id'        => ['db' => 'a.id', 'label' => "Identifiant"],
            'question'  => ['db' => 'a.question', 'label' => "Question"],
            'topics'     => ['db' => 't.label', 'label' => "Topics"],
            'action'    => ['label' => "Action"],
        ];

        return $datatable->getDatatableResponse($request, Faq::class, $data, 'faq/faq', [
            'topics'  => $topics
        ]);
    }

    #[Route('/new', name: 'faq_faq_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exam = new Faq();
        $form = $this->createForm(FaqType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exam);
            $entityManager->flush();

            return $this->redirectToRoute('faq_faq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('faq/faq/new.html.twig', [
            'faq' => $exam,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'faq_faq_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Faq $faq, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FaqType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('faq_faq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('faq/faq/edit.html.twig', [
            'item' => $faq,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'faq_faq_delete', methods: ['POST'])]
    public function delete(Request $request, Faq $faq, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$faq->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($faq);
            $entityManager->flush();
        }

        return $this->redirectToRoute('faq_faq_index', [], Response::HTTP_SEE_OTHER);
    }
}
