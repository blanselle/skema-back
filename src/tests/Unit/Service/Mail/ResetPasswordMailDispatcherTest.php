<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Mail;

use App\Entity\Bloc\Bloc;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Bloc\BlocRewriter;
use App\Service\Mail\MailerEngine;
use App\Service\Mail\ResetPasswordMailDispatcher;
use App\Service\User\TokenManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class ResetPasswordMailDispatcherTest extends TestCase
{
    private ResetPasswordMailDispatcher $resetPasswordMailDispatcher;
    
    private MailerInterface|MockObject $mailer;
    private ParameterBagInterface|MockObject $params;
    private BlocRewriter|MockObject $blocRewriter;
    private UserRepository|MockObject $userRepository;
    private TokenManager|MockObject $tokenManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mailer = $this->createMock(MailerEngine::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->blocRewriter = $this->createMock(BlocRewriter::class);
        $this->params = $this->createMock(ParameterBagInterface::class);
        $this->tokenManager = $this->createMock(TokenManager::class);

        $this->resetPasswordMailDispatcher = new ResetPasswordMailDispatcher(
            $this->mailer,
            $this->userRepository,
            $this->blocRewriter,
            $this->params,
            $this->tokenManager,
        );
    }

    public function testSendMailOk(): void
    {
        $this->tokenManager
            ->expects($this->once())
            ->method('create')
            ->willReturn('token');
        ;

        $this->params
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function($param) {
                $this->assertSame('reset_password_url', $param);
                return 'https://www.dernierepage.com';
            })
        ;

        $this->mailer
            ->expects($this->once())
            ->method('dispatch')
        ;
        
        $this->blocRewriter
            ->expects($this->once())
            ->method('rewriteBloc')
            ->willReturnCallback(function($param) {

                $this->assertSame('MAIL_RESET_PASSWORD', $param);

                return (new Bloc())
                    ->setLabel('label')
                    ->setContent('Content')
                ;
            })    
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneByEmail'))
            ->willReturn((new User())
                ->setEmail('admin@skema.fr')
            );
        ;

        $this->resetPasswordMailDispatcher->dispatch('admin@skema.fr');
    }

    public function testSendMailUserNotFound(): void
    {
        $this->blocRewriter->expects($this->never())->method('rewriteBloc');
        $this->tokenManager->expects($this->never())->method('create');
        $this->params->expects($this->never())->method('get');
        $this->mailer->expects($this->never())->method('dispatch');

        $this->userRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneByEmail'))
            ->willReturn(null);
        ;

        $this->expectException(UserNotFoundException::class);

        $this->resetPasswordMailDispatcher->dispatch('admin@skema.fr');
    }
}