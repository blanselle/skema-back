<?php

declare(strict_types=1);

namespace App\Controller\AdministrativeRecord;

use App\Entity\AdministrativeRecord\SportLevel;
use App\Form\AdministrativeRecord\SportLevelType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/sport_levels')]
#[IsGranted('ROLE_RESPONSABLE')]
class SportLevelController extends AbstractController
{
    #[Route('', name: 'sport_level_index', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('sportLevel/index.html.twig', [
            'sportLevels' => $em
                ->getRepository(SportLevel::class)
                ->findBy([], ['createdAt' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'sport_level_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sportLevel = new SportLevel();
        $form = $this->createForm(SportLevelType::class, $sportLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sportLevel);
            $entityManager->flush();

            return $this->redirectToRoute('sport_level_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sportLevel/new.html.twig', [
            'sportLevel' => $sportLevel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'sport_level_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SportLevel $sportLevel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SportLevelType::class, $sportLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('sport_level_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sportLevel/edit.html.twig', [
            'sportLevel' => $sportLevel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'sport_level_delete', methods: ['POST'])]
    public function delete(Request $request, SportLevel $sportLevel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sportLevel->getId(), strval($request->request->get('_token')))) {
            try {
                $entityManager->remove($sportLevel);
                $entityManager->flush();
            } catch (\Exception $e) {
                if ($e instanceof ForeignKeyConstraintViolationException) {
                    $this->addFlash(
                        'error',
                        'Impossible de supprimer ce niveau sportif car il est rattaché à au moins un dossier administratif'
                    );
                }
            }
        }

        return $this->redirectToRoute('sport_level_index', [], Response::HTTP_SEE_OTHER);
    }
}
