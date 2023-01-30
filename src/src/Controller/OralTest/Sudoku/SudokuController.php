<?php

declare(strict_types=1);

namespace App\Controller\OralTest\Sudoku;

use App\Entity\OralTest\SudokuConfiguration;
use App\Exception\Sudoku\ContestJuryClientException;
use App\Form\OralTest\Sudoku\PlanningInfoSearchType;
use App\Repository\OralTest\SudokuConfigurationRepository;
use App\Repository\ProgramChannelRepository;
use App\Service\OralTest\ContestJuryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/oral-test/sudoku')]
#[IsGranted('ROLE_COORDINATOR')]
class SudokuController extends AbstractController
{
    #[Route('', name: 'sudoku_index', methods: ['GET'])]
    public function index(
        ProgramChannelRepository $programChannelRepository,
        SudokuConfigurationRepository $sudokuConfigurationRepository): Response
    {
        $hasRemainingConfiguration = count($programChannelRepository->findRemainingSudokuProgramChannels()) > 0;
        $sudokuList = $sudokuConfigurationRepository->getAvailableConfiguration();
        $configurations = $sudokuConfigurationRepository->findAll();

        return $this->render('oral_test/sudoku/index.html.twig', [
            'hasRemainingConfiguration' => $hasRemainingConfiguration,
            'sudokuList' => $sudokuList,
            'configurations' => $configurations,
        ]);
    }

    #[Route('/{id}', name: 'sudoku_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(SudokuConfiguration $sudoku): Response
    {
        return $this->render('oral_test/sudoku/show.html.twig', [
            'sudoku' => $sudoku,
        ]);
    }

    #[Route('/test_api_jury', name: 'sudoku_test_api_jury', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function testApiJury(ContestJuryService $contestJuryService, Request $request): Response
    {
        $form = $this->createForm(PlanningInfoSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $data = $form->getData();
            try {
                $planningInfoData = $contestJuryService->getPlanningInfo($data['contestJuryWebsiteCode'], $data['date']);
                if ($planningInfoData === null) {
                    $this->addFlash('error', 'Aucun jury paramétré pour le centre et le jour sélectionné.');
                } else {
                    $this->addFlash('success', 'Un nouveau planning a été généré.');
                }
            } catch (ContestJuryClientException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->renderForm('oral_test/sudoku/test_api_jury.html.twig', [
            'form' => $form,
        ]);
    }
}