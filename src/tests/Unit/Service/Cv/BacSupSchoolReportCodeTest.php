<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Cv;

use App\Constants\CV\BacSupConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\SchoolReport;
use App\Entity\Media;
use App\Repository\CV\BacSupRepository;
use App\Service\Cv\BacSupSchoolReportCode;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BacSupSchoolReportCodeTest extends TestCase
{
    private BacSupSchoolReportCode $bacSupSchoolReportCode;
    private BacSupRepository|MockObject $bacSupRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bacSupRepository = $this->createMock(BacSupRepository::class);
        $this->bacSupSchoolReportCode = $this->bacSupbacSupSchoolReportCodeLevel = new BacSupSchoolReportCode($this->bacSupRepository);
    }

    public function testWith0SchoolReport(): void
    {
        $this->bacSupRepository
            ->expects($this->once())
            ->method('getMainsBacSup')
            ->willReturn([])
        ;
        $schoolReport = (new SchoolReport())
            ->setId(1)
            ->setBacSup((new BacSup())
                ->setId(1)
                ->setType(BacSupConstants::TYPE_SEMESTRIAL)
                ->setCv((new Cv())->setId(1))
            )
        ;

        $result = $this->bacSupSchoolReportCode->get($schoolReport);

        $this->assertEquals('bulletin_L1_S1', $result);
    }


    public function testWithL1S1(): void
    {
        $bacSup = (new BacSup())
            ->setId(1)
            ->setCv((new Cv())->setId(1))
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setId(1)
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                    ->setCode(MediaCodeConstants::CODE_BULLETIN_L1_S1)
                )
            );
        $this->bacSupRepository
            ->expects($this->once())
            ->method('getMainsBacSup')
            ->willReturn([$bacSup])
        ;
        $schoolReport = (new SchoolReport())
            ->setBacSup($bacSup)
        ;

        $result = $this->bacSupSchoolReportCode->get($schoolReport);

        $this->assertEquals('bulletin_L1_S2', $result);
    }

    public function testWithMaxGetAnError(): void
    {
        $bacSup = (new BacSup())
            ->setCv((new Cv())->setId(1))
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setId(1)
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_TO_CHECK)
                    ->setCode(MediaCodeConstants::CODE_AUTRE)
                )
            );
        $this->bacSupRepository
            ->expects($this->once())
            ->method('getMainsBacSup')
            ->willReturn([
                (new BacSup())
                    ->setId(1)
                    ->setCv((new Cv())->setId(1))
                    ->setType(BacSupConstants::TYPE_ANNUAL)
                    ->addSchoolReport((new SchoolReport())
                        ->setId(1)
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                            ->setCode(MediaCodeConstants::CODE_BULLETIN_L1)
                        )
                    ),
                (new BacSup())
                    ->setId(2)
                    ->setCv((new Cv())->setId(1))
                    ->setType(BacSupConstants::TYPE_ANNUAL)
                    ->addSchoolReport((new SchoolReport())
                        ->setId(2)
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                            ->setCode(MediaCodeConstants::CODE_BULLETIN_L2)
                        )
                    ),
                (new BacSup())
                    ->setId(3)
                    ->setCv((new Cv())->setId(1))
                    ->setType(BacSupConstants::TYPE_ANNUAL)
                    ->addSchoolReport((new SchoolReport())
                        ->setId(3)
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                            ->setCode(MediaCodeConstants::CODE_BULLETIN_L3)
                        )
                    ),
                (new BacSup())
                    ->setId(4)
                    ->setCv((new Cv())->setId(1))
                    ->setType(BacSupConstants::TYPE_ANNUAL)
                    ->addSchoolReport((new SchoolReport())
                        ->setId(4)
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                            ->setCode(MediaCodeConstants::CODE_BULLETIN_M1)
                        )
                    ),
                (new BacSup())
                    ->setId(5)
                    ->setCv((new Cv())->setId(1))
                    ->setType(BacSupConstants::TYPE_ANNUAL)
                    ->addSchoolReport((new SchoolReport())
                        ->setId(5)
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                            ->setCode(MediaCodeConstants::CODE_BULLETIN_M2)
                        )
                    )
            ])
        ;
        $schoolReport = (new SchoolReport())
            ->setBacSup($bacSup)
        ;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Media code constant not found');
        $this->bacSupSchoolReportCode->get($schoolReport);
    }

    public function testInvalidbacSupTypeType(): void
    {
        $schoolReport = (new SchoolReport())
            ->setBacSup((new BacSup())
                ->setId(1)
                ->setType('invalid')
            )
        ;
        $this->bacSupRepository
            ->expects($this->never())
            ->method('getMainsBacSup')
        ;
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid BacSup type');
        $this->bacSupSchoolReportCode->get($schoolReport);
    }
}