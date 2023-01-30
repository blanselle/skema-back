<?php

declare(strict_types=1);

namespace App\Tests\Unit\Admissibility\Rule;

use App\Entity\Student;
use App\Ruler\CV\CvRuler;
use App\Ruler\CV\CVRulerInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class RulerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRuler(): void
    {
        $cvRuler = new CvRuler([
            new class implements CVRulerInterface {
                public function getBonus(Student $student): float
                {
                    return 1.3;
                }
            },

            new class implements CVRulerInterface {
                public function getBonus(Student $student): float
                {
                    return 3.6;
                }
            },

            new class implements CVRulerInterface {
                public function getBonus(Student $student): float
                {
                    return 0;
                }
            },

            new class implements CVRulerInterface {
                public function getBonus(Student $student): float
                {
                    throw new Exception('ERROR');

                    return 10;
                }
            },
        ]);

        $this->assertSame(4.9, $cvRuler->getBonus((new Student())));
    }
}