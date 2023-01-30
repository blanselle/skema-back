<?php

namespace App\Controller\OralTest\Sudoku;

use App\Entity\Campus;
use App\Entity\OralTest\SudokuConfiguration;
use App\Form\OralTest\Sudoku\CampusConfigurationType;
use App\Form\OralTest\Sudoku\SudokuConfigurationType;
use App\Repository\CampusRepository;
use App\Repository\OralTest\CampusConfigurationRepository;
use App\Repository\OralTest\SudokuConfigurationRepository;
use App\Repository\ProgramChannelRepository;
use App\Service\OralTest\CampusConfigurationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/oral-test/sudoku/initialization')]
#[IsGranted('ROLE_RESPONSABLE')]
class SudokuInitializationController extends AbstractController
{
    public function __construct(private SudokuConfigurationRepository $sudokuConfigurationRepository) {}

    #[Route('', name: 'sudoku_initialization_init', methods: ['GET', 'POST'])]
    public function init(Request $request, ProgramChannelRepository $programChannelRepository): Response
    {
        if (count($programChannelRepository->findRemainingSudokuProgramChannels()) === 0) {
            $this->addFlash(type: 'warning', message: 'Toutes les voies de concours ont été initialisées.');
            return $this->redirectToRoute('sudoku_index');
        }

        $configuration = new SudokuConfiguration();
        $form = $this->createForm(SudokuConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->sudokuConfigurationRepository->save(entity: $configuration, flush: true);

            return $this->redirectToRoute('sudoku_initialization_configure', ['id' => $configuration->getId()]);
        }

        return $this->renderForm('oral_test/sudoku/init.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/configure', name: 'sudoku_initialization_configure', methods: ['GET', 'POST'])]
    public function configure(
        SudokuConfiguration $sudoku,
        CampusRepository $campusRepository
    ): Response
    {
        $campuses = $campusRepository->getOralTestCampusesWithCapacity(programChannels: $sudoku->getProgramChannels()->toArray());
        if (count($campuses) === 0) {
            return $this->redirectToRoute('campus_capacity_settings_index');
        }

        return $this->renderForm('oral_test/sudoku/configure.html.twig', [
            'sudoku' => $sudoku,
            'campuses' => $campuses,
        ]);
    }

    #[Route('/{sudoku}/init_campus_configuration_form/{campus}', name: 'sudoku_initialization_init_campus_configuration_form', methods: ['GET', 'POST'])]
    public function initCampusConfigurationForm(
        Request $request,
        SudokuConfiguration $sudoku,
        Campus $campus,
        CampusConfigurationRepository $campusConfigurationRepository,
        CampusConfigurationManager $campusConfigurationManager
    ): Response
    {
        $configuration = $campusConfigurationRepository->findOneBy(['sudokuConfiguration' => $sudoku, 'campus' => $campus]);
        if (null === $configuration) {
            $configuration = $campusConfigurationManager->create(campus: $campus);
            $sudoku->addCampusConfiguration($configuration);
        }

        $campusConfigurationRepository->save(entity: $configuration);

        $form = $this->createForm(CampusConfigurationType::class, $configuration, [
            'method' => 'post',
            'action' => $this->generateUrl('sudoku_initialization_init_campus_configuration_form', ['sudoku' => $sudoku->getId(), 'campus' => $campus->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $this->sudokuConfigurationRepository->save(entity: $sudoku, flush: true);
            $message = sprintf('Votre paramétrage a bien été sauvegardé.');
        }

        return $this->renderForm('oral_test/sudoku/_init_campus_configuration_form.html.twig', [
            'form' => $form,
            'message' => $message?? null,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 400 : 200,
        ));
    }
}