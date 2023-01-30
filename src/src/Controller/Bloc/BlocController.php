<?php

declare(strict_types=1);

namespace App\Controller\Bloc;

use App\Entity\Bloc\Bloc;
use App\Form\BlocType;
use App\Repository\BlocRepository;
use App\Repository\ProgramChannelRepository;
use App\Service\Datatable;
use App\Service\Media\MediaUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blocs')]
#[IsGranted('ROLE_COORDINATOR')]
class BlocController extends AbstractController
{
    #[Route('/', name: 'bloc_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Datatable $datatable): Response
    {
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'label'     => is_string($datatable->filter('label')) && !empty($datatable->filter('label'))
                ? html_entity_decode($datatable->filter('label')) : null,
            'tag'     => $datatable->filter('tag'),
            'key'     => $datatable->filter('key'),
        ]);

        $data['columns'] = [
            'label'    => ['db' => 'a.label', 'label' => "Label"],
            'tag'      => ['db' => 't.label', 'label' => "Tag"],
            'key'      => ['db' => 'a.key', 'label' => "Identifiant"],
        ];

        return $datatable->getDatatableResponse($request, Bloc::class, $data, 'bloc');
    }

    #[Route('/new', name: 'bloc_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        BlocRepository $blocRepository,
        ProgramChannelRepository $programChannelRepository
    ): Response
    {
        $bloc = new Bloc();
        $programChannels = $programChannelRepository->findAll();
        foreach ($programChannels as $programChannel) {
            $bloc->addProgramChannel($programChannel);
        }
        $form = $this->createForm(BlocType::class, $bloc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $bloc->getMedia() && empty($bloc->getMedia()->getFormFile())) {
                $bloc->setMedia(null);
            }
            $blocRepository->add($bloc, true);

            return $this->redirectToRoute('bloc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bloc/new.html.twig', [
            'item' => $bloc,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'bloc_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Bloc $bloc,
        BlocRepository $blocRepository,
        MediaUploader $mediaUploader
    ): Response
    {
        $form = $this->createForm(BlocType::class, $bloc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null == $bloc->getMedia()?->getFile() && empty($bloc->getMedia()?->getFormFile())) {
                $bloc->setMedia(null);
            }
            if (null !== $bloc->getMedia() && !empty($bloc->getMedia()->getFormFile())) {
                $mediaUploader->upload($bloc->getMedia());
            }

            $blocRepository->add($bloc, true);

            return $this->redirectToRoute('bloc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bloc/edit.html.twig', [
            'item' => $bloc,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'bloc_delete', methods: ['POST'])]
    public function delete(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bloc->getId(), strval($request->request->get('_token')))) {
            $blocRepository->remove($bloc, true);
        }

        return $this->redirectToRoute('bloc_index', [], Response::HTTP_SEE_OTHER);
    }
}
