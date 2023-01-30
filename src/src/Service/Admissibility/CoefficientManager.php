<?php

namespace App\Service\Admissibility;

use App\Repository\Admissibility\Ranking\CoefficientRepository;

class CoefficientManager
{
    public function __construct(private CoefficientRepository $coefficientRepository) {}

    public function getCoefficientParams(?array $programChannels = []): array
    {
        $list = [];
        $coefficients = $this->coefficientRepository->getCoefficientParams(programChannels: $programChannels);
        foreach ($coefficients as $coefficient) {
            $list[$coefficient['position_key']][$coefficient['type']] = ['value' => $coefficient['coefficient'], 'program_channel' => $coefficient['name'], 'program_channel_key' => $coefficient['program_channel_key']];
        }

        return $list;
    }
}