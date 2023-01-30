<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\Entity;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityTest extends ApiTestCase
{
    protected const ASC = 1;
    protected const DESC = -1;

    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
    }
    protected function checkOrder(array $items, string $fieldPosition, int $sens): bool
    {
        $position = $items[0][$fieldPosition];
        $newPosition = null;
        for($i = 1; $i < count($items); $i++) {
            $newPosition = $items[$i][$fieldPosition];
            if(($newPosition - $position) * $sens <= 0) {
                return false;
            }
            $position = $newPosition;
        }

        return true;
    }
}