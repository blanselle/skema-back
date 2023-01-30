<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\AdministrativeRecord\ScholarShipLevel;
use App\Entity\AdministrativeRecord\SportLevel;
use App\Entity\Exam\ExamLanguage;
use App\Entity\Media;
use App\Entity\Parameter\Parameter;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\ParameterManager;
use App\Repository\MediaRepository;
use App\Security\AdministrativeRecordVoter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdministrativeRecordVoterTest extends TestCase
{
    private ParameterManager|MockObject $parameterManager;
    private LoggerInterface|MockObject $logger;
    private MediaRepository|MockObject $mediaRepository;
    private TokenInterface|MockObject $token;

    private AdministrativeRecordVoter $administrativeRecordVoter;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->parameterManager = $this->createMock(ParameterManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mediaRepository = $this->createMock(MediaRepository::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->token->expects($this->once())->method('getUser')->willReturn((new User())->setStudent((new Student())->setProgramChannel(new ProgramChannel())));

        $this->administrativeRecordVoter = new AdministrativeRecordVoter(
            $this->parameterManager,
            $this->logger,
            $this->mediaRepository,
        );
    }

    public function testDateCloutureOutdatedFalse(): void
    {
        $this->mediaRepository->expects($this->never())->method('findBy');
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider(outdated: true));

        $administrativeRecord = $this->administrativeRecordProvider();
        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'original' => $administrativeRecord,
                'object' => clone $administrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }

    public function testScholarShipLevelChangedTrue(): void
    {
        $media = new Media();
        $this->mediaRepository->expects($this->any())->method('findBy')->willReturn([$media]);
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $administrativeRecord = $this->administrativeRecordProvider();
        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'original' => $administrativeRecord,
                'object' => (clone $administrativeRecord)
                    ->addHighLevelSportsmanMedia($media)
                    ->addScholarShipMedia($media)
                    ->addIdCard($media)
                    ->addThirdTimeMedia($media)
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }

    public function testScholarShipLevelChangedFalse(): void
    {
        $oldAdministrativeRecord = $this->administrativeRecordProvider()
        
        ;

        $newAdministrativeRecord = (clone $oldAdministrativeRecord)
            ->setScholarShip(null)
        ;

        $this->mediaRepository->expects($this->any())->method('findBy')->willReturn([]);
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $administrativeRecord = $this->administrativeRecordProvider();
        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'original' => $administrativeRecord,
                'object' => $newAdministrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(-1, $result);
    }

    public function testJdcRejectedChangedTrue(): void
    {
        $oldAdministrativeRecord = $this->administrativeRecordProvider()
            ->setJdc((new Media())->setState(MediaWorflowStateConstants::STATE_REJECTED))
        ;

        $newAdministrativeRecord = (clone $oldAdministrativeRecord)
            ->setJdc((new Media())->setState(MediaWorflowStateConstants::STATE_TO_CHECK))
        ;

        $this->mediaRepository->expects($this->any())->method('findBy')->willReturn([]);
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'object' => $newAdministrativeRecord,
                'original' => $oldAdministrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }


    public function testJdcChangedFalse(): void
    {
        $oldAdministrativeRecord = $this->administrativeRecordProvider()
            ->setJdc((new Media())->setState(MediaWorflowStateConstants::STATE_TO_CHECK))
        ;

        $newAdministrativeRecord = (clone $oldAdministrativeRecord)
            ->setJdc((new Media())->setState(MediaWorflowStateConstants::STATE_TO_CHECK))
        ;

        $this->mediaRepository->expects($this->any())->method('findBy')->willReturn([]);
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'object' => $newAdministrativeRecord,
                'original' => $oldAdministrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(-1, $result);
    }

    public function testThirdTimeDetailChangedFalse(): void
    {
        $oldAdministrativeRecord = $this->administrativeRecordProvider()
        
        ;

        $newAdministrativeRecord = (clone $oldAdministrativeRecord)
            ->setThirdTimeDetail('changement')
        ;

        $this->mediaRepository->expects($this->any())->method('findBy')->willReturnCallback(function($params) {
            return [];
        });
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'object' => $newAdministrativeRecord,
                'original' => $oldAdministrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(-1, $result);
    }

    public function testAddThirdTimeMediaIgnored(): void
    {
        $newMedia = (new Media())->setState(MediaWorflowStateConstants::STATE_TO_CHECK);

        $originalAdministrativeRecord = ($this->administrativeRecordProvider())
        
        ;
        $newAdministrativeRecord = (clone $originalAdministrativeRecord)
            ->addThirdTimeMedia($newMedia)
        ;

        $this->mediaRepository->expects($this->any())->method('findBy')->willReturnCallback(function($param) use ($newMedia) {
            
            if('tt' === $param['code']) {
                return [$newMedia];
            }

            return [];
        });

        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'object' => $newAdministrativeRecord,
                'original' => $originalAdministrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }

    public function testAddThirdTimeMediaWithCancelledMediaIgnored(): void
    {
        $newMedia = (new Media())->setState(MediaWorflowStateConstants::STATE_TO_CHECK);

        $originalAdministrativeRecord = ($this->administrativeRecordProvider())
        
        ;
        $newAdministrativeRecord = (clone $originalAdministrativeRecord)
            ->addThirdTimeMedia($newMedia)
        ;

        $this->mediaRepository->expects($this->any())->method('findBy')->willReturnCallback(function($param) use ($newMedia) {

            $result = [(new Media())->setState(MediaWorflowStateConstants::STATE_CANCELLED)];

            if('tt' === $param['code']) {
                $result[] = $newMedia;
            }

            return $result;
        });

        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'object' => $newAdministrativeRecord,
                'original' => $originalAdministrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }

    public function testRemoveThirdTimeMediaIgnored(): void
    {
        $removedMedia = (new Media())->setState(MediaWorflowStateConstants::STATE_TO_CHECK);
        
        $originalAdministrativeRecord = ($this->administrativeRecordProvider())
        
        ;
        $newAdministrativeRecord = (clone $originalAdministrativeRecord)

        ;

        $this->mediaRepository->expects($this->any())->method('findBy')->willReturnCallback(function($param) use ($removedMedia) {
            if('tt' === $param['code']) {
                return [(new $removedMedia)];
            }
            return [];
        });

        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $result = $this->administrativeRecordVoter->vote(
            $this->token, 
            [
                'object' => $newAdministrativeRecord,
                'original' => $originalAdministrativeRecord,
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }

    public function administrativeRecordProvider(): AdministrativeRecord
    {
        return (new AdministrativeRecord())
            ->setExamLanguage(new ExamLanguage())
            ->setHighLevelSportsman(true)
            ->setId(1)
            ->setJdc(new Media())
            ->setOptionalExamLanguage(true)
            ->setScholarShip(true)
            ->setScholarShipLevel(new ScholarShipLevel())
            ->setStudent(new Student())
            ->setStudentDiplomas(new ArrayCollection())
            ->setThirdTime(true)
            ->setThirdTimeDetail('aze')
            ->setSportLevel(new SportLevel())
        ;
    }

    public function dateClotureInscriptionsProvider(bool $outdated = false): Parameter
    {
        $date = new DateTime();
        if($outdated) {
            $date->modify('+1 month');
        } else {
            $date->modify('-1 month');
        }
        return (new Parameter)->setValue($date);
    }
}