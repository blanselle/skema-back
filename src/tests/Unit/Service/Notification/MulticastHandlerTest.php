<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Notification;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Bloc\Bloc;
use App\Entity\Student;
use App\Exception\Bloc\BlocNotFoundException;
use App\Handler\MulticastHandler;
use App\Message\MulticastMessage;
use App\Repository\BlocRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MulticastHandlerTest extends TestCase
{
    private StudentRepository|MockObject $studentRepository;
    private UserRepository|MockObject $userRepository;
    private NotificationTransformer|MockObject $notificationTransformer;
    private MessageBusInterface|MockObject $bus;

    private MulticastHandler $multicastHandler; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentRepository = $this->createMock(StudentRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->notificationTransformer = $this->createMock(NotificationTransformer::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->multicastHandler = new MulticastHandler(
            $this->studentRepository,
            $this->userRepository,
            $this->notificationTransformer,
            $this->bus,
        );
    }

    public function testDispatchWithNoStudentIsOk(): void
    {
        $this->studentRepository->expects($this->once())->method('findBy')->willReturn([]); 
        $this->userRepository->expects($this->never())->method('findOneBy');
        $this->notificationTransformer->expects($this->never())->method('transform');
        $this->bus->expects($this->never())->method('dispatch');

        $this->multicastHandler->__invoke((new MulticastMessage())
            ->setSubject('Lorem ipsum')
            ->setContent('Lorem ipsum dolor sit amet')
            ->setStudentIds([])
        );
    }

    public function testDispatchWithStudentIdIsOk(): void
    {
        $this->studentRepository->expects($this->once())->method('findBy')->willReturn([]);        
        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn([]);
        $this->bus->expects($this->never())->method('dispatch');
        
        $this->multicastHandler->__invoke((new MulticastMessage())
            ->setSubject('Lorem ipsum')
            ->setContent('Lorem ipsum dolor sit amet')
            ->setStudentIds([])
            ->setSenderId('3')
        );
    }

    public function testDispatchIsOk(): void
    {
        $this->studentRepository->expects($this->once())->method('findBy')->willReturn([(new Student()), (new Student())]); 
        $this->userRepository->expects($this->never())->method('findOneBy');
        $this->notificationTransformer->expects($this->exactly(2))->method('transform');
        $this->bus->expects($this->exactly(2))->method('dispatch')->willReturn((new Envelope((object) array('1' => 'foo'))));

        $this->multicastHandler->__invoke((new MulticastMessage())
            ->setSubject('Lorem ipsum')
            ->setContent('Lorem ipsum dolor sit amet')
            ->setStudentIds([1, 2])
        );
    }

    public function testDispatchWithRoleSenderIsOk(): void
    {
        $this->studentRepository->expects($this->once())->method('findBy')->willReturn([(new Student()), (new Student())]); 
        $this->userRepository->expects($this->never())->method('findOneBy');
        $this->notificationTransformer->expects($this->exactly(2))->method('transform');
        $this->bus->expects($this->exactly(2))->method('dispatch')->willReturn((new Envelope((object) array('1' => 'foo'))));

        $this->multicastHandler->__invoke((new MulticastMessage())
            ->setSubject('Lorem ipsum')
            ->setContent('Lorem ipsum dolor sit amet')
            ->setStudentIds([1, 2])
            ->setRoleSender(['ROLE_ADMIN'])
        );
    }
}