<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Notification;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Model\Notification\MulticastNotification;
use App\Service\Notification\MulticasterDispatcher;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MulticasterDispatcherTest extends TestCase
{
    private MessageBusInterface|MockObject $bus;

    private MulticasterDispatcher $notificationDispatcherByStudentState; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->notificationDispatcherByStudentState = new MulticasterDispatcher(
            $this->bus,
        );
    }

    public function testDispatchIsOk(): void
    {
        $this->bus->expects($this->once())->method('dispatch')->willReturn((new Envelope((object) array('1' => 'foo'))));

        $multicastNotification = (new MulticastNotification())
            ->setSubject('Lorem ipsum')
            ->setContent('Lorem ipsum dolor sit amet')
        ;
        $this->notificationDispatcherByStudentState->dispatch(
            $multicastNotification,
            [1]
        );
    }
}