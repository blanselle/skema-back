<?php

declare(strict_types=1);

namespace App\Controller\Admissibility;

use App\Constants\Admissibility\CalculatorTypeConstants;
use App\Exception\Admissibility\AdmissibilityNotFoundException;
use App\Exception\Admissibility\CoefficientNotFoundException;
use App\Form\Admissibility\Ranking\RankingType;
use App\Repository\Admissibility\CalculatorRepository;
use App\Service\Admissibility\CoefficientManager;
use App\Service\Admissibility\Ranking\RankingExcelGenerator;
use App\Service\Admissibility\RankingManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/admissibility/ranking')]
class RankingController extends AbstractController
{
    #[Route('', name: 'admissibility_ranking_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        RankingManager $rankingManager,
        RankingExcelGenerator $rankingExcelGenerator,
        CalculatorRepository $calculatorRepository,
        CoefficientManager $coefficientManager
    ): Response
    {
        $calculator = $calculatorRepository->findOneBy(['type' => CalculatorTypeConstants::TYPE_RANKING_SIMULATOR]);
        $calculatorIsRunning = $calculator?->isRunning()?? false;
        $ranking = [];

        $form = $this->createForm(RankingType::class, null, ['calculatorIsRunning' => $calculatorIsRunning]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($calculatorIsRunning) {
                $this->addFlash('warning', $calculator->getMessage());
                return $this->redirectToRoute('admissibility_ranking_index');
            }

            $programChannels = $form->get('programChannels')->getData()->toArray();

            usort($programChannels, function($programChannel1, $programChannel2){
                if ($programChannel1->getKey() == $programChannel2->getKey()) {
                    return 0;
                }
                return ($programChannel1->getKey() < $programChannel2->getKey()) ? -1 : 1;
            });

            $coefficients = $coefficientManager->getCoefficientParams(programChannels: $programChannels);
            /** @var Form $form */
            if (null !== $form->getClickedButton()) {
                /** @var FormInterface $button */
                $button = $form->getClickedButton();
                switch ($button->getName()) {
                    case RankingType::BUTTON_SIMULATE:

                        try {
                            $ranking = $rankingManager->execute(programChannels: $programChannels, coefficients: $coefficients);
                        } catch (AdmissibilityNotFoundException|CoefficientNotFoundException $e) {
                            $this->addFlash('error', $e->getMessage());
                        }
                        break;
                    case RankingType::BUTTON_EXPORT:
                        $filename = sprintf('export-ranking-%s.xlsx', date('YmdHis'));
                        $tempFile = $rankingExcelGenerator->export(coefficients: $coefficients, filename: $filename, programChannels: $programChannels);

                        return $this->file($tempFile, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
                }
            }
        } else {
            $coefficients = $coefficientManager->getCoefficientParams();
            $form->remove('export');
            if ($calculator !== null) {
                $this->addFlash($calculatorIsRunning ? 'warning' : 'info', $calculator->getMessage());
            }
        }

        return $this->renderForm('admissibility/ranking/index.html.twig', [
            'form' => $form,
            'ranking' => $ranking,
            'coefficients' => $coefficients,
        ]);
    }
}