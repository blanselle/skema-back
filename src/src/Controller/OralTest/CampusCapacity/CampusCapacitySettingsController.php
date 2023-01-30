<?php

namespace App\Controller\OralTest\CampusCapacity;

use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Form\OralTest\CampusOralDay\ProgramChannelSettingsForm;
use App\Repository\CampusRepository;
use App\Repository\OralTest\CampusOralDayConfigurationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/oral_test/campus_capacity/settings')]
#[IsGranted('ROLE_COORDINATOR')]
class CampusCapacitySettingsController extends AbstractController
{
    public function __construct(private CampusRepository $campusRepository, private CampusOralDayConfigurationRepository $configurationRepository) {}

    #[Route('', name: 'campus_capacity_settings_index', methods: ['GET', 'POST'])]
    public function settings(Request $request): Response
    {
        $configurations = $this->configurationRepository->findBy([], ['campus' => 'asc']);
        $campuses = $this->campusRepository->findBy(['oralTestCenter' => true], ['assignmentCampus' => 'DESC', 'name' => 'ASC']);
        $configuration = new CampusOralDayConfiguration();
        $form = $this->createForm(ProgramChannelSettingsForm::class, $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->configurationRepository->save(entity: $configuration, flush: true);

            return $this->redirectToRoute('campus_capacity_settings_languages_index', [
                'id' => $configuration->getId()
            ]);
        }

        return $this->renderForm('oral_test/campus_capacity/setting.html.twig', [
            'campuses' => $campuses,
            'form' => $form,
            'configurations' => $configurations,
        ]);
    }
}