<?php

declare(strict_types=1);

namespace App\Ruler\CV;

use App\Entity\Student;

interface CVRulerInterface
{
    public function getBonus(Student $student): float;
}
