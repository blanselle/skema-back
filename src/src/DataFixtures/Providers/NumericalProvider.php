<?php

namespace App\DataFixtures\Providers;

class NumericalProvider
{
    public function addition(int $current, int $increment): int
    {
        return $current + $increment;
    }

    public function increment(int $current, int $step, ?int $start = null): int
    {
        return ($current * $step) + $start;
    }

    public function randomScore(int $scoreMin, int $scoreMax, int $step): int
    {
        $scores = range($scoreMin, $scoreMax, $step);

        return $scores[mt_rand(0, count($scores)-1)];
    }
}