<?php

namespace App\Service\OralTest;

use App\Entity\Campus;
use App\Entity\OralTest\CampusConfiguration;
use App\Entity\OralTest\SlotConfiguration;
use App\Entity\OralTest\TestConfiguration;
use App\Repository\OralTest\CampusConfigurationRepository;
use App\Repository\OralTest\SlotTypeRepository;
use App\Repository\OralTest\TestTypeRepository;

class CampusConfigurationManager
{
    public function __construct(
        private CampusConfigurationRepository $campusConfigurationRepository,
        private SlotTypeRepository $slotTypeRepository,
        private TestTypeRepository $testTypeRepository
    ) {}

    public function create(Campus $campus): CampusConfiguration
    {
        $configuration = new CampusConfiguration();
        $configuration
            ->setCampus($campus)
        ;

        $testsType = $this->testTypeRepository->findBy([], ['position' => 'ASC']);
        $slotsTypes = $this->slotTypeRepository->findBy([], ['position' => 'ASC']);

        foreach ($testsType as $testType) {
            $testConfiguration = new TestConfiguration();
            $testConfiguration->setTestType($testType);

            foreach ($slotsTypes as $slotType) {
                $slotConfiguration = new SlotConfiguration();
                $slotConfiguration->setSlotType($slotType);
                $testConfiguration->addSlotConfiguration($slotConfiguration);
            }

            $configuration->addTestConfiguration($testConfiguration);
        }

        $this->campusConfigurationRepository->save(entity: $configuration);

        return $configuration;
    }
}