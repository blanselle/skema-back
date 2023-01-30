<?php

namespace App\Service\Admissibility\Ranking;

interface DataToExportInterface
{
    public function generate(array $coefficients, array $programChannels): array;
}