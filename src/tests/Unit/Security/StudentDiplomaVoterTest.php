<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Parameter\Parameter;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\ParameterManager;
use App\Repository\MediaRepository;
use App\Security\StudentDiplomaVoter;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class StudentDiplomaVoterTest extends TestCase
{
    private ParameterManager|MockObject $parameterManager;
    private LoggerInterface|MockObject $logger;
    private MediaRepository|MockObject $mediaRepository;
    private TokenInterface|MockObject $token;

    private StudentDiplomaVoter $studentDiplomaVoter;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->parameterManager = $this->createMock(ParameterManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mediaRepository = $this->createMock(MediaRepository::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->token->expects($this->once())->method('getUser')->willReturn((new User())->setStudent((new Student())->setProgramChannel(new ProgramChannel())));

        $this->studentDiplomaVoter = new StudentDiplomaVoter(
            $this->parameterManager,
            $this->logger,
            $this->mediaRepository,
        );
    }

    public function testDateCloutureOutdatedFalse(): void
    {
        $this->mediaRepository->expects($this->never())->method('findBy');
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider(outdated: true));

        $studentDiploma = $this->studentDiplomaProvider();
        $result = $this->studentDiplomaVoter->vote(
            $this->token, 
            [
                'original' => $studentDiploma,
                'object' => clone $studentDiploma,
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }

    public function testNoChangementTrue(): void
    {
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $studentDiploma = $this->studentDiplomaProvider();
        $result = $this->studentDiplomaVoter->vote(
            $this->token, 
            [
                'original' => $studentDiploma,
                'object' => (clone $studentDiploma),
            ],
            ['edit']
        );

        $this->assertSame(1, $result);
    }

    public function testYearChangedFalse(): void
    {
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $studentDiploma = $this->studentDiplomaProvider();
        $result = $this->studentDiplomaVoter->vote(
            $this->token, 
            [
                'original' => $studentDiploma,
                'object' => (clone $studentDiploma)->setYear(2048),
            ],
            ['edit']
        );

        $this->assertSame(-1, $result);
    }

    public function testDiplomaChannelChangedFalse(): void
    {
        $this->parameterManager->expects($this->once())->method('getParameter')->willReturn($this->dateClotureInscriptionsProvider());

        $studentDiploma = $this->studentDiplomaProvider();
        $result = $this->studentDiplomaVoter->vote(
            $this->token, 
            [
                'object' => (clone $studentDiploma),
                'original' => $studentDiploma->setDiplomaChannel((new DiplomaChannel())),
            ],
            ['edit']
        );

        $this->assertSame(-1, $result);
    }

    public function studentDiplomaProvider(): StudentDiploma
    {
        return (new StudentDiploma())
            ->setId(1)
            ->setYear(2018)
            ->setDiplomaChannel(new DiplomaChannel())
            ->setEstablishment('establishment')
            ->setPostalCode('75000')
            ->setCity('OmerVille')
            ->setDetail('detail')
            ->setDiploma(new Diploma())
            ->setLastDiploma(true)
            ->setAdministrativeRecord(new AdministrativeRecord())
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