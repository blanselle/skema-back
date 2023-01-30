<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service\Admissibility\Bonus;

use App\Constants\Admissibility\Bonus\BonusListConstants;
use App\Service\Admissibility\GetAllBonuses;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetAllBonusesTest extends KernelTestCase
{
    private GetAllBonuses $getAllBonuses;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getAllBonuses = new GetAllBonuses(
            $this->getContainer()->get(EntityManagerInterface::class),
        );
    }

    public function testGetAllBonuses(): void
    {
        $bonuses = $this->getAllBonuses->get([]);

        $bonusTypes = array_flip(BonusListConstants::getConsts());

        $this->assertTrue(!empty($bonuses));

        foreach($bonuses as $bonus) {
            $this->assertTrue(isset($bonusTypes[get_class($bonus)]));
        }
    }
}