<?php

declare(strict_types=1);

namespace App\Controller\Diploma;

use App\Entity\Diploma\DiplomaChannel;
use App\Form\Diploma\DiplomaChannelType;
use App\Repository\Diploma\DiplomaChannelRepository;
use App\Repository\Diploma\DiplomaRepository;
use App\Service\Datatable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/diploma/channels')]
#[IsGranted('ROLE_RESPONSABLE')]
class DiplomaChannelController extends AbstractController
{
    #[Route('', name: 'diploma_channel_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        DiplomaChannelRepository $diplomaChannelRepository,
        DiplomaRepository $diplomaRepository,
        Datatable $datatable
    ): Response {
        $diploma = null;
        $idDiploma = $request->get('idDiploma', null);
        if (!is_null($idDiploma) && is_numeric($idDiploma)) {
            $diploma = $diplomaRepository->findOneById($idDiploma);
        }

        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'name'      => is_string($datatable->filter('name')) && !empty($datatable->filter('name'))
                ? html_entity_decode($datatable->filter('name')) : null,
            'idDiploma' => $datatable->filter('idDiploma'),
        ]);

        $data['columns'] = [
            'name'      => ['db' => 'a.name', 'label' => "Nom de la filière"],
            'count'     => ['label' => "Diplômes rattachés"],
            'action'    => ['label' => "Actions"],
        ];

        return $datatable->getDatatableResponse($request, DiplomaChannel::class, $data, 'diploma/diploma_channel', ['diploma' => $diploma]);
    }

    #[Route('/new', name: 'diploma_channel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $diplomaChannel = new DiplomaChannel();
        $form = $this->createForm(DiplomaChannelType::class, $diplomaChannel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($diplomaChannel);
            $entityManager->flush();

            return $this->redirectToRoute('diploma_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diploma/diploma_channel/new.html.twig', [
            'diploma_channel' => $diplomaChannel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'diploma_channel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DiplomaChannel $diplomaChannel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DiplomaChannelType::class, $diplomaChannel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('diploma_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diploma/diploma_channel/edit.html.twig', [
            'item' => $diplomaChannel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'diploma_channel_delete', methods: ['POST'])]
    public function delete(Request $request, DiplomaChannel $diplomaChannel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$diplomaChannel->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($diplomaChannel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('diploma_channel_index', [], Response::HTTP_SEE_OTHER);
    }
}
