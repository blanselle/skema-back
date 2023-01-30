<?php

declare(strict_types=1);

namespace App\Controller\Diploma;

use App\Entity\Diploma\Diploma;
use App\Entity\ProgramChannel;
use App\Form\Diploma\DiplomaType;
use App\Repository\Diploma\DiplomaChannelRepository;
use App\Repository\Diploma\DiplomaRepository;
use App\Service\Datatable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/diplomas')]
#[IsGranted('ROLE_RESPONSABLE')]
class DiplomaController extends AbstractController
{
    #[Route('', name: 'diploma_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        DiplomaRepository $diplomaRepository,
        DiplomaChannelRepository $diplomaChannelRepository,
        EntityManagerInterface $em,
        Datatable $datatable
    ): Response {
        $diplomaChannel = null;
        $programChannels = $em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'asc']);
        $idDiplomaChannel = $request->get('idDiplomaChannel', null);
        if (!is_null($idDiplomaChannel) && is_numeric($idDiplomaChannel)) {
            $diplomaChannel = $diplomaChannelRepository->findOneById($idDiplomaChannel);
        }

        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'name'              => is_string($datatable->filter('name')) && !empty($datatable->filter('name'))
                ? html_entity_decode($datatable->filter('name')) : null,
            'programChannel'    => $datatable->filter('programChannel'),
            'idDiplomaChannel'  => $datatable->filter('idDiplomaChannel'),
        ]);

        $data['columns'] = [
            'diploma'           => ['db' => 'a.name', 'label' => "Nom du diplôme"],
            'count'             => ['label' => "Nombre de filières"],
            'programChannels'   => ['label' => "Voie de concours"],
            'action'            => ['label' => "Actions"],
        ];

        return $datatable->getDatatableResponse($request, Diploma::class, $data, 'diploma/diploma', [
            'diplomaChannel' => $diplomaChannel,
            'programChannels' => $programChannels,
        ]);
    }

    #[Route('/new', name: 'diploma_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $diploma = new Diploma();
        $form = $this->createForm(DiplomaType::class, $diploma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($diploma);
            $entityManager->flush();

            return $this->redirectToRoute('diploma_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diploma/diploma/new.html.twig', [
            'diploma' => $diploma,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'diploma_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Diploma $diploma, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DiplomaType::class, $diploma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('diploma_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('diploma/diploma/edit.html.twig', [
            'item' => $diploma,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'diploma_delete', methods: ['POST'])]
    public function delete(Request $request, Diploma $diploma, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$diploma->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($diploma);
            $entityManager->flush();
        }

        return $this->redirectToRoute('diploma_index', [], Response::HTTP_SEE_OTHER);
    }
}
