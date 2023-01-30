<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Constants\Parameters\ParametersKeyConstants;
use App\Constants\Parameters\ParametersKeyTypeConstants;
use App\Entity\Parameter\Parameter;
use App\Entity\Parameter\ParameterKey;
use App\Entity\ProgramChannel;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Manager\ParameterManager;
use App\Repository\Parameter\ParameterRepository;
use App\Repository\ProgramChannelRepository;
use Monolog\Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ParameterManagerTest extends TestCase
{
    private ParameterRepository|MockObject $parameterRepository;
    private ProgramChannelRepository|MockObject $programChannelRepository;

    private ParameterManager $parameterManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parameterRepository = $this->createMock(ParameterRepository::class);
        $this->programChannelRepository = $this->createMock(ProgramChannelRepository::class);

        $this->parameterManager = new ParameterManager(
            $this->parameterRepository,
            $this->programChannelRepository,
        );
    }

    public function testGetParameterWithProgramChannel(): void
    {
        $this->parameterRepository
            ->expects($this->once())
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn((new Parameter())
                ->setKey((new ParameterKey())
                    ->setType(ParametersKeyTypeConstants::DATE)
                )
            )
        ;
        $this->parameterRepository
            ->expects($this->never())
            ->method('findOneParameterByKeyName')
        ;
        $this->parameterManager->getParameter('dateDebutInscriptions', new ProgramChannel());
    }

    public function testGetParameterWithProgramChannelId(): void
    {
        $this->parameterRepository
            ->expects($this->never())
            ->method('findOneParameterByKeyNameAndProgramChannel')
        ;
        $this->parameterRepository
            ->expects($this->once())
            ->method('findOneParameterByKeyName')
            ->willReturn((new Parameter()))
        ;
        $this->parameterManager->getParameter('dateDebutInscriptions');
    }

    public function testGetParameterNotFound(): void
    {
        $this->parameterRepository
            ->expects($this->never())
            ->method('findOneParameterByKeyNameAndProgramChannel')
        ;
        $this->parameterRepository
            ->expects($this->once())
            ->method('findOneParameterByKeyName')
            ->willReturn(null)
        ;
        $this->expectException(ParameterNotFoundException::class);
        $this->parameterManager->getParameter('dateDebutInscriptions');
    }
}