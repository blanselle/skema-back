<?php

declare(strict_types=1);

namespace App\Tests\Functional\Workflow;

use App\Constants\User\SimplifiedStudentStatusConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\Registry;

class StudentWorkflowTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function testWorkflowConstIsFilled(): void
    {
        /** @var Registry $workflowRegistry */
        $workflowRegistry = $this->getContainer()->get(Registry::class);
        $workflow = $workflowRegistry->get((new Student()));

        $states = $workflow->getDefinition()->getPlaces();
        $consts = StudentWorkflowStateConstants::getConsts();

        foreach($consts as $const) {
            $this->assertTrue(isset($states[$const]));
        }

        $this->assertSame(count($states), count($consts), 'The states are not totally reported in constants');
    }

    public function testWorkflowStateIsAllReportedInSimplifiedConst(): void
    {
        $states = StudentWorkflowStateConstants::getConsts();
        $simplifiedStates = [];
        foreach(SimplifiedStudentStatusConstants::getConsts() as $consts) {
            foreach($consts as $const) {
                $simplifiedStates[] = $const;
            }
        }
        
        foreach($states as $state) {
            $this->assertContains($state, $simplifiedStates);
        }

        $this->assertSame(count($simplifiedStates), count($states), 'The states are not totally reported in simplified constants');
    }
}