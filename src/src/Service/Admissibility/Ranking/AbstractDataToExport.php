<?php

namespace App\Service\Admissibility\Ranking;

abstract class AbstractDataToExport
{
    protected function canExport(array $coefficients, string $programChannelPositionKey): bool
    {
        return isset($coefficients[$programChannelPositionKey]);
    }
}