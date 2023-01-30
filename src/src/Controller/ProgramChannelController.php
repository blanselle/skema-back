<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProgramChannel;
use App\Exception\Cv\KeyRemoveException;
use App\Form\ProgramChannelType;
use App\Repository\ProgramChannelRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/programChannels')]
#[IsGranted('ROLE_ADMIN')]
class ProgramChannelController extends AbstractController
{
    #[Route('/', name: 'program_channel_index', methods: ['GET'])]
    public function index(ProgramChannelRepository $programChannelRepository): Response
    {
        return $this->render('program_channel/index.html.twig', [
            'programChannels' => $programChannelRepository->findBy([], ['name' => 'asc']),
        ]);
    }

    #[Route('/new', name: 'program_channel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $programChannel = new ProgramChannel();
        $form = $this->createForm(ProgramChannelType::class, $programChannel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($programChannel);

            try {
                $entityManager->flush();
            } catch(UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'La position est déjà utilisée');
                return $this->renderForm('program_channel/new.html.twig', [
                    'programChannel' => $programChannel,
                    'form' => $form,
                ]);
            }    

            return $this->redirectToRoute('program_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program_channel/new.html.twig', [
            'programChannel' => $programChannel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'program_channel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProgramChannel $programChannel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgramChannelType::class, $programChannel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('program_channel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program_channel/edit.html.twig', [
            'programChannel' => $programChannel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'program_channel_delete', methods: ['POST'])]
    public function delete(Request $request, ProgramChannel $programChannel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programChannel->getId(), strval($request->request->get('_token')))) {
            
            try {
                $entityManager->remove($programChannel);
                $entityManager->flush();
            } catch (KeyRemoveException $e) {
            
                $this->addFlash(
                    'error',
                    'Impossible de supprimer cette voie de concours'
                );
            }
        }

        return $this->redirectToRoute('program_channel_index', [], Response::HTTP_SEE_OTHER);
    }
}
