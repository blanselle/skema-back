<?php

namespace App\Controller\OralTest\CampusCapacity;

use App\Entity\OralTest\CampusOralDay;
use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Repository\OralTest\CampusOralDayConfigurationRepository;
use App\Repository\OralTest\CampusOralDayRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/oral_test/campus_capacity')]
#[IsGranted('ROLE_COORDINATOR')]
class CampusCapacityController extends AbstractController
{
    public function __construct(
        private CampusOralDayRepository $campusOralDayRepository,
        private CampusOralDayConfigurationRepository $configurationRepository
    ) {}

    #[Route('/', name: 'campus_capacity_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('campus_capacity_settings_index');
    }

    #[Route('/{id}', name: 'campus_capacity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(CampusOralDayConfiguration $configuration): Response
    {
        // In case of edit configuration need to remove previous slot
        $this->refreshSlotDays(configuration: $configuration);

        $slotDays = $this->getSlotDays(configuration: $configuration);

        return $this->render('oral_test/campus_capacity/show.html.twig', [
            'configuration' => $configuration,
            'slotDays' => $slotDays,
        ]);
    }

    #[Route('/{id}/refresh_places', name: 'campus_capacity_refresh_places', requirements: ['id' => '\d+'], methods: 'POST')]
    public function refreshPlaces(CampusOralDay $campusOralDay, Request $request, CampusOralDayRepository $repository): Response
    {
        if ($this->isCsrfTokenValid("refresh-places-{$campusOralDay->getId()}", strval($request->request->get('_token')))) {
            // prevents manual entry of a negative or decimal number
            $campusOralDay->setNbOfAvailablePlaces((int)abs(floor($request->request->getInt('nbOfAvailablePlaces'))));
            $repository->save(entity: $campusOralDay, flush: true);

            return $this->render('oral_test/campus_capacity/_campus_slot_row_table.html.twig', [
                'slot' => $campusOralDay,
                'colNumber' => $request->get('colNumber', 0),
            ]);
        }

        return $this->json(['message' => 'Une erreur est survenue!'], Response::HTTP_BAD_REQUEST);
    }

    private function getSlotDays(CampusOralDayConfiguration $configuration): array
    {
        $campusOralDays = $configuration->getCampusOralDays()->toArray();

        usort($campusOralDays, function(CampusOralDay $a, CampusOralDay $b) {
            if (null === $a->getFirstLanguage() and null === $a->getSecondLanguage()) {
                return 1;
            }

            if ($a->getFirstLanguage()?->getName() === $b->getFirstLanguage()?->getName()
            ) {
                return ($a->getSecondLanguage()?->getName() > $b->getSecondLanguage()?->getName())? 1 : -1;
            }

            return ($a->getFirstLanguage()?->getName() > $b->getFirstLanguage()?->getName())? 1 : -1;
        });

        $slotDays = [];
        foreach ($campusOralDays as $campusOralDay) {
            $day = $campusOralDay->getDate()->format('Y-m-d');
            if (empty($slotDays) or !isset($slotDays[$day])) {
                $slotDays[$day] = [];
            }

            $slotDays[$day][] = $campusOralDay;
        }

        ksort($slotDays);

        return $slotDays;
    }

    private function refreshSlotDays(CampusOralDayConfiguration $configuration): void
    {
        $firstConfigurationLanguages = $configuration->getFirstLanguages();
        $secondConfigurationLanguages = $configuration->getSecondLanguages();

        foreach($configuration->getCampusOralDays() as $slot) {
            if (!$configuration->isOptionalLv1()) {
                $slots = $configuration->getCampusOralDays()->filter(function(CampusOralDay $s) {
                    return (null === $s->getFirstLanguage() and null === $s->getSecondLanguage());
                });
                foreach ($slots as $s) {
                    $configuration->removeCampusOralDay(campusOralDay: $s);
                    $this->campusOralDayRepository->remove(entity: $s, flush: true);
                }
            }
            if (
                null !== $slot->getFirstLanguage() and
                !$firstConfigurationLanguages->contains($slot->getFirstLanguage())
            ) {
                $configuration->removeCampusOralDay(campusOralDay: $slot);
                $this->campusOralDayRepository->remove(entity: $slot, flush: true);
                continue;
            }

            if (
                null !== $slot->getSecondLanguage() and
                !$secondConfigurationLanguages->contains($slot->getSecondLanguage())
            ) {
                $configuration->removeCampusOralDay(campusOralDay: $slot);
                $this->campusOralDayRepository->remove(entity: $slot, flush: true);
            }
        }

        $this->configurationRepository->save(entity: $configuration, flush: true);
    }
}