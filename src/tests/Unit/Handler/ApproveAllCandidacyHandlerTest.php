<?php

declare(strict_types=1);

namespace App\Tests\Unit\Handler;

use App\Constants\Notification\NotificationConstants;
use App\Entity\Notification\Notification;
use App\Entity\Student;
use App\Entity\User;
use App\Handler\ApproveAllCandidacyHandler;
use App\Message\ApproveAllCandidacy;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationCenter;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Monolog\Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ApproveAllCandidacyHandlerTest extends TestCase
{
    private StudentRepository|MockObject $studentRepository;
    private StudentWorkflowManager|MockObject $studentWorkflowManager;
    private NotificationCenter|MockObject $notificationCenter;
    private UserRepository|MockObject $userRepository;

    private ApproveAllCandidacyHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentRepository = $this->createMock(StudentRepository::class);
        $this->studentWorkflowManager = $this->createMock(StudentWorkflowManager::class);
        $this->notificationCenter = $this->createMock(NotificationCenter::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->handler = new ApproveAllCandidacyHandler(
            $this->studentRepository,
            $this->studentWorkflowManager,
            $this->notificationCenter,
            $this->userRepository,
        );
    }

    public function testApproveAllCandidayIsOk(): void
    {
        $message = new ApproveAllCandidacy(new Uuid('1ed929de-03aa-6c78-98bf-a32561354851'));

        $students = [
            $this->studentProvider(),
            $this->studentProvider(),
            $this->studentProvider(),
        ];

        $this->studentRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn($students)
        ;


        $this->studentWorkflowManager
            ->expects($this->exactly(count($students)))
            ->method('completeToApproved')
            ->willReturn(true, false, true)
        ;
        
        $this->notificationCenter
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function(Notification $notification, array $transports, bool $sendGenericMail) {
                $this->assertSame('Approbation des candidats terminé', $notification->getSubject());
                $this->assertSame(<<<EOF
                        L’approbation des candidats est terminée. <br />
                    
                        <strong>2</strong> candidatures approuvées. <br />
                    
                        <strong>1</strong> candidatures non approuvées : <br />
                        <ul>
                        <li>  - lastname firstName</li>
                        </ul>
                    EOF, 
                    $notification->getContent()
                );
                $this->assertSame([NotificationConstants::TRANSPORT_DB], $transports);
                $this->assertSame($sendGenericMail, false);
            })
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn(new User())
        ;

        $this->handler->__invoke($message);
    }

    private function studentProvider(): Student
    {
        return (new Student())
            ->setUser((new User())
                ->setLastName('lastname')
                ->setFirstName('firstName')
            )
        ;
    }
}