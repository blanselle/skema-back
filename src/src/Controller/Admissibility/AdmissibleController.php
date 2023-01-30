<?php

namespace App\Controller\Admissibility;

use App\Constants\Admissibility\CalculatorTypeConstants;
use App\Entity\Admissibility\Calculator;
use App\Entity\User;
use App\Form\Admissibility\AdmissibleType;
use App\Message\EligibleStudents;
use App\Repository\Admissibility\CalculatorRepository;
use App\Service\Admissibility\AdmissibilityExportManager;
use App\Service\Admissibility\AdmissibilityManager;
use App\Service\Admissibility\CoefficientManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/admissibility/admissible')]
#[IsGranted('ROLE_RESPONSABLE')]
class AdmissibleController extends AbstractController
{
    #[Route('', name: 'admissibility_admissible_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AdmissibilityManager $admissibilityManager,
        AdmissibilityExportManager $exportManager,
        CoefficientManager $coefficientManager,
        MessageBusInterface $bus,
        CalculatorRepository $calculatorRepository,
        EntityManagerInterface $em
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $calculator = $calculatorRepository->findOneBy(['type' => CalculatorTypeConstants::TYPE_RANKING_ADMISSIBILITY]);
        if (null === $calculator) {
            $calculator = new Calculator(CalculatorTypeConstants::TYPE_RANKING_ADMISSIBILITY);
            $em->persist($calculator);
        }
        $calculator
            ->setUserId($user->getId());

        $em->flush();

        $admissibles = [];

        $form = $this->createForm(AdmissibleType::class, null, ['calculatorIsRunning' => $calculator->isRunning()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programChannels = $form->get('programChannels')->getData()->toArray();
            $score = (float)$form->get('score')->getData();
            $admissibles['eligible_candidates'] = $admissibilityManager->getEligibleStudents(programChannels: $programChannels, score: $score);
            $admissibles['not_eligible_candidates'] = $admissibilityManager->getEligibleStudents(programChannels: $programChannels, score: $score, eligible: false);
            $coefficients = $coefficientManager->getCoefficientParams(programChannels: $programChannels);

            /** @var Form $form */
            if (null !== $form->getClickedButton()) {
                /** @var FormInterface $button */
                $button = $form->getClickedButton();
                switch ($button->getName()) {
                    case AdmissibleType::BUTTON_EXPORT:
                        $tempFile = $exportManager->exportAdmissible($admissibles['eligible_candidates']);
                        return $this->file($tempFile, sprintf('export-admissibles-%s.xlsx', date('YmdHis')), ResponseHeaderBag::DISPOSITION_INLINE);
                    case AdmissibleType::BUTTON_SAVE:
                        $programChannelIds = array_map(function($program) {
                            return $program->getId();
                        }, $programChannels);

                        $calculator->setLastLaunchDate(new DateTime());
                        $em->flush();

                        $bus->dispatch(new EligibleStudents(programChannelIds: $programChannelIds, score: $score, calculatorId: $calculator->getId(), userId: $user->getId()));

                        $this->addFlash('success', 'Vous recevrez une notification lorsque la définition des admissibles sera terminée.');

                        break;
                }
            }
        } else {
            $coefficients = $coefficientManager->getCoefficientParams();
            $form->remove('save');
            $form->remove('export');
            $this->addFlash($calculator->isRunning()? 'warning' : 'info', $calculator->getMessage());
        }

        return $this->renderForm('admissibility/admissible/index.html.twig', [
            'form' => $form,
            'admissibles' => $admissibles,
            'coefficients' => $coefficients
        ]);
    }
}