<?php

declare(strict_types=1);

namespace App\Service\Dashboard;

use App\Constants\Dashboard\DashboardColumnConstants;
use App\Constants\Dashboard\DashboardLabelConstants;
use App\Model\Dashboard\Row;
use App\Repository\StudentRepository;

class DashboardInscriptionCounter
{
    public function __construct(private StudentRepository $studentRepository) {}

    public function getRows(array $programChannels): array
    {
        $nbInscriptionByState = [];
        foreach ($programChannels as $programChannel) {
            $nbInscriptionByState[] = $this->studentRepository->findNbInscriptionByState($programChannel);
        }

        $rows = [];

        foreach (DashboardLabelConstants::getConsts() as $key => $label) {
            $rows[] = (new Row())
                ->setLabel($label)
                ->setValues($this->generateValues(
                    filter: function ($state) use ($key) {
                        return in_array($state, constant(DashboardColumnConstants::class . '::' . $key), true);
                    },
                    elements: $nbInscriptionByState,
                ))
            ;
        }

        return $rows;
    }

    private function generateValues(callable $filter, array $elements): array
    {
        $values = [];
        foreach ($elements as $element) {
            $value = 0;
            foreach ($element as $item) {
                if ($filter($item['state'])) {
                    $value += $item['count'];
                }
            }
            $values[] = $value;
        }

        return $values;
    }
}
