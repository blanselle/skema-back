<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Bloc;

use App\Entity\Bloc\Bloc;
use App\Entity\Parameter\Parameter;
use App\Entity\Parameter\ParameterKey;
use App\Entity\ProgramChannel;
use App\Exception\Bloc\BlocNotFoundException;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Manager\ParameterManager;
use App\Repository\BlocRepository;
use App\Service\Bloc\BlocRewriter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BlocRewriterTest extends TestCase
{

    private ParameterManager|MockObject $parameterManager;
    private BlocRepository|MockObject $blocRepository;

    private BlocRewriter $blocRewriter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blocRepository = $this->createMock(BlocRepository::class);
        $this->parameterManager = $this->createMock(ParameterManager::class);

        $this->blocRewriter = new BlocRewriter(
            $this->parameterManager,
            $this->blocRepository,
        );
    }

    public function testRewriteEmptyBlocIsOk(): void 
    {
        $bloc = (new Bloc())
            ->setContent('Contenu du bloc')
        ;

        $this->parameterManager->expects($this->never())->method('getParameter');
        $this->blocRepository->expects($this->never())->method('findOneBy');

        $rewritedBloc = $this->blocRewriter->rewriteBloc($bloc, null);

        $this->assertSame($bloc->getContent(), $rewritedBloc->getContent());
    }

    public function testRewriteBlocWithParameterArgumentIsOk(): void
    {
        $this->blocRepository->expects($this->never())->method('findActiveByKeyAndProgramChannel');
    
        $bloc = (new Bloc())
            ->setContent('Contenu du bloc de %parameter.argument% %other.argumentofrtesting%')
            ->setLabel('Label du bloc de %parameter.argument%.')
        ;

        $programChannel = (new ProgramChannel())
        
        ;

        $parameterKey = (new ParameterKey())
            ->setName('argument')
        ;

        $parameter = (new Parameter())
            ->setKey($parameterKey)
            ->addProgramChannel($programChannel)
            ->setValue('test')
        ;

        $this->parameterManager
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->willReturn($parameter)
        ;

        $rewritedBloc = $this->blocRewriter->rewriteBloc($bloc, $programChannel);

        $this->assertSame('Contenu du bloc de test %other.argumentofrtesting%', $rewritedBloc->getContent());
        $this->assertSame('Label du bloc de test.', $rewritedBloc->getLabel());
    }

    public function testRewriteBlocWithParameterDateIsOk(): void
    {
        $this->blocRepository->expects($this->never())->method('findActiveByKeyAndProgramChannel');

        $bloc = (new Bloc())
            ->setContent('Test du %parameter.date%')
        ;

        $programChannel = (new ProgramChannel())
        
        ;

        $parameterKey = (new ParameterKey())
            ->setName('date')
        ;

        $parameter = (new Parameter())
            ->setKey($parameterKey)
            ->addProgramChannel($programChannel)
            ->setValue(\DateTime::createFromFormat('Y-m-d H:i', '2021-01-01 11:42'))
        ;

        $this->parameterManager
            ->expects($this->once())
            ->method('getParameter')
            ->willReturn($parameter)
        ;

        $rewritedBloc = $this->blocRewriter->rewriteBloc($bloc, $programChannel);

        $this->assertSame('Test du 1 janvier 2021 à 11:42', $rewritedBloc->getContent());
    }

    public function testRewriteBlocWithUndefinedParameterIsOk(): void
    {
        $this->blocRepository->expects($this->never())->method('findOneBy');

        $bloc = (new Bloc())
            ->setContent('Variable %parameter.date%...')
        ;

        $programChannel = (new ProgramChannel())
        
        ;

        $this->parameterManager
            ->expects($this->once())
            ->method('getParameter')
            ->willThrowException(new ParameterNotFoundException())
        ;
            
        $rewritedBloc = $this->blocRewriter->rewriteBloc($bloc, $programChannel);

        $this->assertSame('Variable undefined...', $rewritedBloc->getContent());
    }

    public function testRewriteBlocWithDefaultParameterIdOk(): void
    {
        $this->blocRepository->expects($this->never())->method('findOneBy');
        $this->parameterManager->expects($this->never())->method('getParameter');

        $bloc = (new Bloc())
            ->setContent('Test de %default.firstname% encore')
        ;
            
        $rewritedBloc = $this->blocRewriter->rewriteBloc($bloc, new ProgramChannel(), ['firstname' => 'Julien']);

        $this->assertSame('Test de Julien encore', $rewritedBloc->getContent());
    }

    public function testRewriteBlocWithUndefinedForParameterProgramChannelIsOk(): void
    {
        $this->blocRepository->expects($this->never())->method('findActiveByKeyAndProgramChannel');

        $bloc = (new Bloc())
            ->setContent('Contenu du bloc de %parameter.argument% %other.argumentofrtesting%')
            ->setLabel('Label du bloc de %parameter.argument%.')
        ;

        $programChannel = (new ProgramChannel())
        
        ;

        $this->parameterManager
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->willThrowException(new ParameterNotFoundException())
        ;
            
        $rewritedBloc = $this->blocRewriter->rewriteBloc($bloc, $programChannel);

        $this->assertSame('Contenu du bloc de undefined %other.argumentofrtesting%', $rewritedBloc->getContent());
        $this->assertSame('Label du bloc de undefined.', $rewritedBloc->getLabel());
    }

    public function testRewriteBlocFromKeyIsOk(): void
    {
        $bloc = (new Bloc())
            ->setContent('Contenu du bloc')
        ;

        $this->blocRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['key' => 'testu'])
            ->willReturn($bloc)
        ;
    
        $rewritedBloc = $this->blocRewriter->rewriteBloc('testu', null);

        $this->assertSame($bloc->getContent(), $rewritedBloc->getContent());
    }

    public function testRewriteBlocFromKeyAndProgramChannelIsOk(): void
    {        
        $bloc = (new Bloc())
            ->setContent('Contenu du bloc')
        ;

        $programChannel = (new ProgramChannel())

        ;

        $this->blocRepository
            ->expects($this->once())
            ->method('findActiveByKeyAndProgramChannel')
            ->with('testu', $programChannel)
            ->willReturn($bloc)
        ;
    
        $rewritedBloc = $this->blocRewriter->rewriteBloc('testu', $programChannel);

        $this->assertSame($bloc->getContent(), $rewritedBloc->getContent());
    }

    public function testRewriteBlocFromKeyBlocNotFoundGetAnError(): void
    {
        $this->blocRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['key' => 'testu'])
            ->willReturn(null)
        ;
    
        $this->expectException(BlocNotFoundException::class);
        $this->blocRewriter->rewriteBloc('testu', null);
    }
}