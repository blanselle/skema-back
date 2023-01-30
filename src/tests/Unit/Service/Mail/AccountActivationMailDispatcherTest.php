<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Mail;

use App\Entity\Bloc\Bloc;
use App\Entity\Student;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Service\Bloc\BlocRewriter;
use App\Service\Mail\MailerEngine;
use App\Service\Mail\AccountActivationMailDispatcher;
use App\Service\User\TokenManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

class AccountActivationMailDispatcherTest extends TestCase
{
    private AccountActivationMailDispatcher $accountActivationMailDispatcher;
    
    private MailerInterface|MockObject $mailer;
    private ParameterBagInterface|MockObject $params;
    private BlocRewriter|MockObject $blocRewriter;
    private TokenManager|MockObject $tokenManager;
    private LoggerInterface|MockObject $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mailer = $this->createMock(MailerEngine::class);
        $this->blocRewriter = $this->createMock(BlocRewriter::class);
        $this->params = $this->createMock(ParameterBagInterface::class);
        $this->tokenManager = $this->createMock(TokenManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->accountActivationMailDispatcher = new AccountActivationMailDispatcher(
            $this->mailer,
            $this->blocRewriter,
            $this->params,
            $this->tokenManager,
            $this->logger,
        );
    }

    public function testSendMailOk(): void
    {
        $this->logger->expects($this->never())->method('critical');
        $this->tokenManager
            ->expects($this->once())
            ->method('create')
            ->willReturn('token');
        ;

        $this->params
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function($param) {
                $this->assertSame('account_activation_url', $param);
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

                $this->assertSame('ACCOUNT_ACTIVATION_MAIL', $param);

                return (new Bloc())
                    ->setLabel('label')
                    ->setContent('Content')
                ;
            })    
        ;

        $this->accountActivationMailDispatcher->dispatch((new Student())
            ->setUser((new User())
                ->setFirstName('Henry')
                ->setEmail('henry4@gmail.com')
            )
        );
    }

    public function testSendMailBlocNotFoundIsOk(): void
    {
        $this->logger->expects($this->once())->method('critical');

        $this->tokenManager
            ->expects($this->once())
            ->method('create')
            ->willReturn('token');
        ;

        $this->params
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function($param) {
                $this->assertSame('account_activation_url', $param);
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
            ->willThrowException(new BlocNotFoundException('ACCOUNT_ACTIVATION_MAIL'))    
        ;

        $this->accountActivationMailDispatcher->dispatch((new Student())
            ->setUser((new User())
                ->setFirstName('Henry')
                ->setEmail('henry4@gmail.com')
            )
        );
    }
}