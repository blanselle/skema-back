<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Entity\Bloc\Bloc;
use App\Entity\Student;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Exception\Notification\NotificationBadRequestException;
use App\Manager\NotificationManager;
use App\Repository\BlocRepository;
use Monolog\Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class NotificationManagerTest extends TestCase
{
    private BlocRepository|MockObject $blocRepository;

    private NotificationManager $notificationManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blocRepository = $this->createMock(BlocRepository::class);        

        $this->notificationManager = new NotificationManager($this->blocRepository);
    }

    public function testCreateNotificationBlocNotFoundGetAnError(): void
    {
        $student = (new Student())
            ->setIdentifier('TEST')
            ->setUser((new User()))
        ;

        $this->blocRepository
            ->expects($this->once())
            ->method('findActiveByKey')
            ->willReturn(null)
        ;

        $this->expectException(BlocNotFoundException::class);
        $this->notificationManager->createNotification(
            receiver: $student->getUser(),
            blocKey: 'invalid-bloc-key',
            params: ['test' => 'test'],
        );
    }

    public function testCreateNotificationIsOk(): void
    {
        $student = (new Student())
            ->setIdentifier('TEST')
            ->setUser((new User()))
        ;

        $bloc = (new Bloc())
            ->setLabel('TEST')
            ->setContent('TEST %var%')
        ;

        $this->blocRepository
            ->expects($this->once())
            ->method('findActiveByKey')
            ->willReturn($bloc)
        ;

        $notification = $this->notificationManager->createNotification(
            sender: $student->getUser(),
            receiver: $student->getUser(),
            blocKey: 'bloc-key',
            params: ['var' => 'ok'],
        );

        $this->assertSame('TEST ok', $notification->getContent());
        $this->assertSame($student->getUser(), $notification->getSender());
        $this->assertSame($student->getUser(), $notification->getReceiver());
    }
}