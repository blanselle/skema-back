<?php

namespace App\Service\Dashboard;

use App\Constants\Dashboard\DashboardAdmissibleLabelConstants;
use App\Model\Dashboard\Row;
use App\Repository\StudentRepository;

class DashboardAdmissibleCounter
{
    public function __construct(private StudentRepository $studentRepository) {}

    public function getRows(array $programChannels): array
    {
        $rows = [];
        foreach (DashboardAdmissibleLabelConstants::getConsts() as $key => $const)
        {
            $nbAdmissible = [];
            $registeredForExams = ($key === 'ADMISSIBLED_REGISTERED');
            foreach ($programChannels as $programChannel) {
                $nbAdmissible[] = $this->studentRepository->getAdmissibleByProgramChannel($programChannel, $registeredForExams);
            }

            $rows[] = (new Row())
                ->setLabel($const)
                ->setValues($this->generateValues($nbAdmissible))
            ;
        }

        return $rows;
    }

    private function generateValues(array $elements): array
    {
        $count = [];
        foreach ($elements as $item) {
            $count[] = count($item);
        }
        return $count;
    }
}