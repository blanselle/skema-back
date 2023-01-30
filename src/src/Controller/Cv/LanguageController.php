<?php

declare(strict_types=1);

namespace App\Controller\Cv;

use App\Service\Datatable;
use App\Entity\CV\Language;
use Symfony\Component\Form\Form;
use App\Form\Admin\User\CV\LanguageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/language')]
#[IsGranted('ROLE_RESPONSABLE')]
class LanguageController extends AbstractController
{
    #[Route('/', name: 'language_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Datatable $datatable): Response
    {
        $data = [];

        $data['filters'] = $datatable->cleanEmptyArray([
            'code'     => $datatable->filter('code'),
            'label'     => is_string($datatable->filter('label')) && !empty($datatable->filter('label'))
                ? html_entity_decode($datatable->filter('label')) : null,
        ]);

        $data['columns'] = [
            'label'    => ['db' => 'a.label', 'label' => "Label"],
            'code'    => ['db' => 'a.code', 'label' => "Code"],
            'action'    => ['label' => "Actions"],
        ];

        return $datatable->getDatatableResponse($request, Language::class, $data, 'language');
    }

    #[Route('/new', name: 'language_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $language = new Language();
        /** @var Form $form $form */
        $form = $this->createForm(LanguageType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($language);
            $entityManager->flush();

            return $this->redirectToRoute('language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('language/new.html.twig', [
            'item' => $language,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'language_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Language $language, EntityManagerInterface $em): Response
    {
        /** @var Form $form $form */
        $form = $this->createForm(LanguageType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('language/edit.html.twig', [
            'item' => $language,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'language_delete', methods: ['POST'])]
    public function delete(Request $request, Language $language, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$language->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($language);
            $entityManager->flush();
        }

        return $this->redirectToRoute('language_index');
    }
}
