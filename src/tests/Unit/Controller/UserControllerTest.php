<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\UserController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Router;
use  Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use  Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends AbstractControllerTest
{
    private FormFactory|MockObject $formFactory;
    private RouterInterface|MockObject $router;
    private CsrfTokenManagerInterface|MockObject $csrfTokenManager;
    private UserRepository|MockObject $userRepository;
    private EntityManagerInterface|MockObject $em;

    private UserController $controller;
    
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

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->em = $this->createMock(EntityManager::class);
        $this->encoderInterface = $this->createMock(UserPasswordHasherInterface::class);
    
        $this->controller = new UserController();
        $this->controller->setContainer($this->container);
    }

    public function testIndexOk(): void
    {
        /** @var PaginatorInterface|MockObject */
        $paginator = $this->createMock(PaginatorInterface::class);

        $paginator
            ->expects($this->once())
            ->method('paginate')
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('findAllExceptCandidate')
            ->willReturn([new User()])
        ;
        
        $request = new Request(['page' => 12]);

        $content = $this->controller->index($request, $this->userRepository, $paginator);
        $this->assertSame(Response::HTTP_OK, $content->getStatusCode());
    }

    public function testNewNotSubmited(): void
    {
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');
        
        $content = $this->controller->new(new Request(), $this->em, $this->encoderInterface);
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

        $content = $this->controller->new(new Request(), $this->em, $this->encoderInterface);
        $this->assertSame(Response::HTTP_SEE_OTHER, $content->getStatusCode());
    }
    
    public function testEditNotSubmited(): void
    {
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');
        
        $content = $this->controller->edit(new Request(), new User(), $this->em);
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

        $content = $this->controller->edit(new Request(), new User(), $this->em);
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

        $user = (new User())
            ->setId(new Uuid('1eca45ec-a33c-6456-a4ae-499880a42597'))
        ;

        $request = new Request(request: ['_token']);

        $content = $this->controller->delete($request, $user, $this->em);
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

        $user = (new User())
            ->setId(new Uuid('1eca45ec-a33c-6456-a4ae-499880a42597'))
        ;

        $request = new Request(request: ['_token']);

        $content = $this->controller->delete($request, $user, $this->em);
        $this->assertSame(Response::HTTP_SEE_OTHER, $content->getStatusCode());
    }
}