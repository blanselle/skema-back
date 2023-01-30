<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Notification;

use App\Constants\Notification\NotificationConstants;
use App\Entity\Notification\Notification;
use App\Service\Notification\DbNotification;
use App\Service\Notification\MailNotification;
use App\Service\Notification\Notifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NotifierTest extends TestCase
{
    private MailNotification|MockObject $mailNotification;
    private DbNotification|MockObject $dbNotification;

    private Notifier $notifier; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->mailNotification = $this->createMock(MailNotification::class);
        $this->dbNotification = $this->createMock(DbNotification::class);

        $this->notifier = new Notifier(
            $this->mailNotification,
            $this->dbNotification,
        );
    }

    public function testSendDbIsOk(): void
    {

        $notification = new Notification();

        $this->dbNotification->expects($this->once())->method('send')->with($notification);
        $this->mailNotification->expects($this->never())->method('send');
        
        $this->notifier->send(
            $notification,
            [
                NotificationConstants::TRANSPORT_DB,
            ]
        ); 
    }

    public function testSendMailIsOk(): void
    {

        $notification = new Notification();

        $this->mailNotification->expects($this->once())->method('send')->with($notification);
        $this->dbNotification->expects($this->never())->method('send');
        
        $this->notifier->send(
            $notification,
            [
                NotificationConstants::TRANSPORT_EMAIL,
            ]
        ); 
    }

    public function testSendDbAndMailIsOk(): void
    {
        $notification = new Notification();

        $this->dbNotification->expects($this->once())->method('send')->with($notification);
        $this->mailNotification->expects($this->once())->method('send')->with($notification);
        
        $this->notifier->send(
            $notification,
            [
                NotificationConstants::TRANSPORT_DB,
                NotificationConstants::TRANSPORT_EMAIL,
                
            ]
        ); 
    }

    public function testSendNoTransportIsOk(): void
    {
        $notification = new Notification();

        $this->mailNotification->expects($this->never())->method('send');
        $this->mailNotification->expects($this->never())->method('send');
        
        $this->notifier->send(
            $notification,
            []
        ); 
    }

    public function testSendInvalidTransportIsOk(): void
    {
        $notification = new Notification();

        $this->mailNotification->expects($this->never())->method('send');
        $this->mailNotification->expects($this->never())->method('send');
        
        $this->notifier->send(
            $notification,
            [
                'invalid-transport'
            ]
        ); 
    }
}