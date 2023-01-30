<?php

namespace App\Controller\OralTest\CampusCapacity;

use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Form\OralTest\CampusOralDay\LanguageSettingsForm;
use App\Repository\OralTest\CampusOralDayConfigurationRepository;
use App\Service\OralTest\CampusOralDayManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/oral_test/campus_capacity/settings_languages')]
#[IsGranted('ROLE_COORDINATOR')]
class CampusCapacitySettingsLanguagesController extends AbstractController
{
    public function __construct(
        private CampusOralDayManager $campusOralDayManager,
        private CampusOralDayConfigurationRepository $configurationRepository
    ) {}
    #[Route('/{id}', name: 'campus_capacity_settings_languages_index', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function index(CampusOralDayConfiguration $configuration, Request $request): Response
    {
        $form = $this->createForm(LanguageSettingsForm::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->configurationRepository->save(entity: $configuration, flush: true);
            // remove campus oral day
            $this->campusOralDayManager->deleteCampusOralDays(configuration: $configuration);

            try {
                $this->campusOralDayManager->performCampusOralDays(configuration: $configuration);
            } catch (ParameterNotFoundException $e) {
                $this->addFlash(type: 'error', message: $e->getMessage());
                return $this->redirectToRoute('campus_capacity_settings_index');
            }

            return $this->redirectToRoute('campus_capacity_show', [
                'id' => $configuration->getId()
            ]);
        }

        return $this->renderForm('oral_test/campus_capacity/settings_languages.html.twig', [
            'configuration' => $configuration,
            'campus' => $configuration->getCampus(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}/check_reserved_places', name: 'campus_capacity_settings_languages_check_reserved_places', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function checkReservedPlaces(CampusOralDayConfiguration $configuaration, Request $request): Response
    {
        /** @var int|null $firstLanguageId */
        $firstLanguageId = $request->request->get('firstLanguageId');
        /** @var int|null $secondLanguageId */
        $secondLanguageId = $request->request->get('secondLanguageId');
        if (null === $firstLanguageId and null === $secondLanguageId) {
            throw new BadRequestException('Vous devez sélectionner au moins une langue.');
        }

        $count = $this->campusOralDayManager->getNumberOfReservedPlaces(configuration: $configuaration, firstLanguageId: $firstLanguageId, secondLanguageId: $secondLanguageId);

        if ($count > 0) {
            return $this->json(['message' => "Vous ne pouvez pas supprimer cette langue car {$count} candidat(s) est(sont) inscrit(s) sur une session."], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([]);
    }

}