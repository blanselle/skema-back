<?php

declare(strict_types=1);

namespace App\Controller\Campus;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use App\Service\Media\MediaUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/campuses')]
#[IsGranted('ROLE_ADMIN')]
class CampusController extends AbstractController
{
    #[Route('/', name: 'campus_index', methods: ['GET'])]
    public function index(CampusRepository $campusRepository): Response
    {
        return $this->render('campus/index.html.twig', [
            'campuses' => $campusRepository->findBy([], ['createdAt' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'campus_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        /** @var Form $form $form */
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $campus->getMedia() && empty($campus->getMedia()->getFormFile())) {
                $campus->setMedia(null);
            }

            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'campus_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Campus $campus, EntityManagerInterface $em, MediaUploader $mediaUploader): Response
    {
        /** @var Form $form $form */
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $campus->getMedia() && !empty($campus->getMedia()->getFormFile())) {
                $mediaUploader->upload($campus->getMedia());
            }
            $em->flush();

            return $this->redirectToRoute('campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'campus_delete', methods: ['POST'])]
    public function delete(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($campus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('campus_index');
    }
}
