<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Notification;

use App\Entity\Bloc\Bloc;
use App\Entity\Notification\Notification;
use App\Exception\Bloc\BlocNotFoundException;
use App\Handler\NotificationHandler;
use App\Message\NotificationMessage;
use App\Repository\BlocRepository;
use App\Service\Notification\NotificationTransformer;
use App\Service\Notification\Notifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NotificationHandlerTest extends TestCase
{
    private Notifier|MockObject $notifier;
    private NotificationTransformer|MockObject $notificationTransformer;
    private BlocRepository|MockObject $blocRepository;

    private NotificationHandler $notificationHandler; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifier = $this->createMock(Notifier::class);
        $this->notificationTransformer = $this->createMock(NotificationTransformer::class);
        $this->blocRepository = $this->createMock(BlocRepository::class);

        $this->notificationHandler = new NotificationHandler(
            $this->notifier,
            $this->notificationTransformer,
            $this->blocRepository,
        );
    }

    public function testInvokeWithoutGenericMailIsOk(): void
    {
        $notificationMessage = new NotificationMessage();
        $notification = new Notification();
        $codes = ['a', 'b'];

        $this->notificationTransformer
            ->expects($this->once())
            ->method('reverse')
            ->with($notificationMessage)
            ->willReturn([
                $notification, 
                $codes,
                false,
            ])
        ;

        $this->notifier->expects($this->once())->method('send')->with($notification, $codes);

        $this->notificationHandler->__invoke($notificationMessage);    
    }

    public function testInvokeWithGenericMailIsOk(): void
    {
        $notificationMessage = new NotificationMessage();
        $notification = new Notification();
        $codes = ['a', 'b'];

        $this->notificationTransformer
            ->expects($this->once())
            ->method('reverse')
            ->with($notificationMessage)
            ->willReturn([
                $notification, 
                $codes,
                true
            ])
        ;

        $this->blocRepository
            ->expects($this->once())
            ->method('findActiveByKey')
            ->willReturn((new Bloc())
                ->setLabel('Title')
                ->setContent('Content')
            )
        ;

        $this->notifier->expects($this->exactly(2))->method('send');

        $this->notificationHandler->__invoke($notificationMessage);    
    }

    public function testInvokeMissingBlocGetAnerror(): void
    {
        $notificationMessage = new NotificationMessage();
        $notification = new Notification();
        $codes = ['a', 'b'];

        $this->notificationTransformer
            ->expects($this->once())
            ->method('reverse')
            ->with($notificationMessage)
            ->willReturn([
                $notification, 
                $codes,
                true
            ])
        ;

        $this->blocRepository
            ->expects($this->once())
            ->method('findActiveByKey')
            ->willReturn(null)
        ;

        $this->notifier->expects($this->never())->method('send');
        
        $this->expectException(BlocNotFoundException::class);
        
        $this->notificationHandler->__invoke($notificationMessage);    
    }
}