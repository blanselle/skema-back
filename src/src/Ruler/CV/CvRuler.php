<?php

declare(strict_types=1);

namespace App\Ruler\CV;

use App\Entity\Student;
use Exception;

class CvRuler
{
    public function __construct(private iterable $rules)
    {
    }

    public function getBonus(Student $student): float
    {
        $bonus = 0;
        foreach ($this->rules as $rule) {
            try {
                $bonus += $rule->getBonus($student);
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }

        return $bonus;
    }
}
