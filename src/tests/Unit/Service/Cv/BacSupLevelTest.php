<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Cv;

use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Repository\CV\BacSupRepository;
use App\Service\Cv\BacSupLevel;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BacSupLevelTest extends TestCase
{
    private BacSupRepository|MockObject $bacSupRepository;
    private EntityManagerInterface|MockObject $em;

    private BacSupLevel $bacSupLevel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bacSupRepository = $this->createMock(BacSupRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->bacSupLevel = $this->bacSupLevel = new BacSupLevel(
            $this->em,
            $this->bacSupRepository,
        );
    }

    public function testExistingBacSup1IsOk(): void
    {
        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([
                (new BacSup())
                    ->setId(1)
            ])
        ;

        $this->em
            ->expects($this->once())
            ->method('contains')
            ->willReturn(true)
        ;

        $bacSup = (new BacSup())
            ->setId(1)
            ->setCv((new Cv()))
        ;

        $result = $this->bacSupLevel->get($bacSup);

        $this->assertEquals(1, $result);
    }

    public function testNotExistingBacSup1IsOk(): void
    {
        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([])
        ;

        $this->em
            ->expects($this->once())
            ->method('contains')
            ->willReturn(false)
        ;

        $bacSup = (new BacSup())
            ->setId(1)
            ->setCv((new Cv()))
        ;

        $result = $this->bacSupLevel->get($bacSup);

        $this->assertEquals(1, $result);
    }

    public function testExistingBacSup2IsOk(): void
    {
        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([
                (new BacSup())
                    ->setId(2),
                (new BacSup())
                    ->setId(1)
            ])
        ;

        $this->em
            ->expects($this->once())
            ->method('contains')
            ->willReturn(true)
        ;

        $bacSup = (new BacSup())
            ->setId(1)
            ->setCv((new Cv()))
        ;

        $result = $this->bacSupLevel->get($bacSup);

        $this->assertEquals(2, $result);
    }

    public function testNotExistingBacSup2IsOk(): void
    {
        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([
                (new BacSup())
                    ->setId(2)
            ])
        ;

        $this->em
            ->expects($this->once())
            ->method('contains')
            ->willReturn(false)
        ;

        $bacSup = (new BacSup())
            ->setId(1)
            ->setCv((new Cv()))
        ;

        $result = $this->bacSupLevel->get($bacSup);

        $this->assertEquals(2, $result);
    }

    public function testNotExistingBacSup3WithCustomBacSupsListIsOk(): void
    {
        $this->bacSupRepository->expects($this->never())->method('findBy');

        $this->em
            ->expects($this->once())
            ->method('contains')
            ->willReturn(false)
        ;

        $bacSup = (new BacSup())
            ->setId(3)
            ->setCv((new Cv()))
        ;

        $result = $this->bacSupLevel->get(
            $bacSup,
            [
                (new BacSup())
                    ->setId(2),
                (new BacSup())
                    ->setId(1)
            ],
        );

        $this->assertEquals(3, $result);
    }

    public function testExistingBacSup3WithCustomBacSupsListIsOk(): void
    {
        $this->bacSupRepository->expects($this->never())->method('findBy');

        $this->em
            ->expects($this->once())
            ->method('contains')
            ->willReturn(true)
        ;

        $bacSup = (new BacSup())
            ->setId(3)
            ->setCv((new Cv()))
        ;

        $result = $this->bacSupLevel->get(
            $bacSup,
            [
                (new BacSup())
                    ->setId(2),
                (new BacSup())
                    ->setId(1),
                $bacSup
            ],
        );

        $this->assertEquals(3, $result);
    }

    public function testDualBacSupIsOk(): void
    {
        $this->bacSupRepository->expects($this->never())->method('findBy');

        $this->em
            ->expects($this->once())
            ->method('contains')
            ->willReturn(true)
        ;

        $bacSupParent = (new BacSup())
            ->setId(2);
        $bacSup = (new BacSup())
            ->setId(3)
            ->setCv((new Cv()))
            ->setParent($bacSupParent)
        ;

        $result = $this->bacSupLevel->get(
            $bacSup,
            [
                (new BacSup())
                    ->setId(1),
                $bacSupParent,
                $bacSup
            ],
        );

        $this->assertEquals(2, $result);
    }
}