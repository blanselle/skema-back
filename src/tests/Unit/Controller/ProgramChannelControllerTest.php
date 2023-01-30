<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ProgramChannelController;
use App\Entity\ProgramChannel;
use App\Repository\ProgramChannelRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Router;
use  Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use  Symfony\Component\Security\Csrf\CsrfTokenManager;

class ProgramChannelControllerTest extends AbstractControllerTest
{
    private FormFactory|MockObject $formFactory;
    private RouterInterface|MockObject $router;
    private CsrfTokenManagerInterface|MockObject $csrfTokenManager;
    private ProgramChannelRepository|MockObject $programChannelRepository;
    private EntityManagerInterface|MockObject $em;

    private ProgramChannelController $controller;
    
    protected function setUp(): void
    {
        // Ajout des modules supplémentaires qui sont injectés dans le AbstractControllerTest
        $this->router = $this->createMock(Router::class);
        $this->formFactory = $this->createMock(FormFactory::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManager::class);
        $this->modules['form.factory'] = $this->formFactory;
        $this->modules['router'] = $this->router;
        $this->modules['security.csrf.token_manager'] = $this->csrfTokenManager;
    
        parent::setUp();

        $this->programChannelRepository = $this->createMock(ProgramChannelRepository::class);
        $this->em = $this->createMock(EntityManager::class);
    
        $this->controller = new ProgramChannelController();
        $this->controller->setContainer($this->container);
    }

    public function testIndexOk(): void
    {
        $this->programChannelRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([new ProgramChannel()])
        ;
        
        $content = $this->controller->index($this->programChannelRepository);
        $this->assertSame(Response::HTTP_OK, $content->getStatusCode());
    }

    public function testNewNotSubmited(): void
    {
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');
        
        $content = $this->controller->new(new Request(), $this->em);
        $this->assertSame(Response::HTTP_OK, $content->getStatusCode());
    }
    
    public function testNewSubmited(): void
    {
        /** @var Form|MockObject */
        $form = $this->createMock(Form::class);

        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->willReturn('http://site.com')
        ;

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($form)
        ;

        $content = $this->controller->new(new Request(), $this->em);
        $this->assertSame(Response::HTTP_SEE_OTHER, $content->getStatusCode());
    }
    
    public function testEditNotSubmited(): void
    {
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');
        
        $content = $this->controller->edit(new Request(), new ProgramChannel(), $this->em);
        $this->assertSame(Response::HTTP_OK, $content->getStatusCode());
    }

    public function testEditSubmited(): void
    {
        /** @var Form|MockObject */
        $form = $this->createMock(Form::class);

        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $this->em->expects($this->never())->method('persist'); // Pas besoin de persister quand on edit un objet
        $this->em->expects($this->once())->method('flush');

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->willReturn('http://site.com')
        ;

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($form)
        ;

        $content = $this->controller->edit(new Request(), new ProgramChannel(), $this->em);
        $this->assertSame(Response::HTTP_SEE_OTHER, $content->getStatusCode());
    }

    public function testDeleteOk(): void
    {
        $this->em->expects($this->once())->method('remove');
        $this->em->expects($this->once())->method('flush');

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->willReturn('http://site.com')
        ;

        $this->csrfTokenManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(true)
        ;

        $programChannel = (new ProgramChannel())
            ->setId(10)
        ;

        $request = new Request(request: ['_token']);

        $content = $this->controller->delete($request, $programChannel, $this->em);
        $this->assertSame(Response::HTTP_SEE_OTHER, $content->getStatusCode());
    }

    public function testDeleteTokenCsrfInvalid(): void
    {
        $this->em->expects($this->never())->method('remove');
        $this->em->expects($this->never())->method('flush');

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->willReturn('http://site.com')
        ;

        $this->csrfTokenManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(false)
        ;

        $programChannel = (new ProgramChannel())
            ->setId(10)
        ;

        $request = new Request(request: ['_token']);

        $content = $this->controller->delete($request, $programChannel, $this->em);
        $this->assertSame(Response::HTTP_SEE_OTHER, $content->getStatusCode());
    }
}