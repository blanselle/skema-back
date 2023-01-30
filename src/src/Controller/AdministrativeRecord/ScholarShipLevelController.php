<?php

declare(strict_types=1);

namespace App\Controller\AdministrativeRecord;

use App\Entity\AdministrativeRecord\ScholarShipLevel;
use App\Form\AdministrativeRecord\ScholarShipLevelType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/scholar_ship_levels')]
#[IsGranted('ROLE_RESPONSABLE')]
class ScholarShipLevelController extends AbstractController
{
    #[Route('', name: 'scholar_ship_level_index', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('scholarShipLevel/index.html.twig', [
            'scholarShipLevels' => $em
                ->getRepository(ScholarShipLevel::class)
                ->findBy([], ['createdAt' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'scholar_ship_level_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $scholarShipLevel = new ScholarShipLevel();
        $form = $this->createForm(ScholarShipLevelType::class, $scholarShipLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($scholarShipLevel);
            $entityManager->flush();

            return $this->redirectToRoute('scholar_ship_level_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('scholarShipLevel/new.html.twig', [
            'scholarShipLevel' => $scholarShipLevel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'scholar_ship_level_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ScholarShipLevel $scholarShipLevel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScholarShipLevelType::class, $scholarShipLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('scholar_ship_level_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('scholarShipLevel/edit.html.twig', [
            'scholarShipLevel' => $scholarShipLevel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'scholar_ship_level_delete', methods: ['POST'])]
    public function delete(Request $request, ScholarShipLevel $scholarShipLevel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scholarShipLevel->getId(), strval($request->request->get('_token')))) {
            try {
                $entityManager->remove($scholarShipLevel);
                $entityManager->flush();
            } catch (\Exception $e) {
                if ($e instanceof ForeignKeyConstraintViolationException) {
                    $this->addFlash(
                        'error',
                        'Impossible de supprimer ce niveau de bourse car il est rattaché à au moins un dossier administratif'
                    );
                }
            }
        }

        return $this->redirectToRoute('scholar_ship_level_index', [], Response::HTTP_SEE_OTHER);
    }
}
