<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Notification;

use App\Constants\Media\MediaCodeConstants;
use App\Entity\Bloc\Bloc;
use App\Entity\Media;
use App\Entity\Notification\Notification;
use App\Entity\Student;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Manager\NotificationManager;
use App\Repository\BlocRepository;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationMedia;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationMediaTest extends TestCase
{
    private BlocRepository|MockObject $blocRepository;
    private TranslatorInterface|MockObject $translator;
    private UserRepository|MockObject $userRepository;
    private Security|MockObject $security;
    private NotificationManager|MockObject $notificationManager;


    private NotificationMedia $notificationMedia;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->blocRepository = $this->createMock(BlocRepository::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->notificationManager = $this->createMock(NotificationManager::class);
        
        $this->notificationMedia = new NotificationMedia(
            $this->blocRepository,
            $this->translator,
            $this->userRepository,
            $this->security,
            $this->notificationManager,
        );
    }
    
    public function testGenerateRejectedNotificationBlocNotFoundGetAnError(): void
    {
        $this->blocRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->userRepository->expects($this->never())->method('findOneBy');
        $this->security->expects($this->never())->method('getUser');
        $this->translator->expects($this->never())->method('trans');
        $this->notificationManager->expects($this->never())->method('createNotification');

        $this->expectException(BlocNotFoundException::class);

        $this->notificationMedia->generateRejectedNotification((new Media()), 'subject', 'content');
    }

    public function testGenerateRejectedNotificationIsOk(): void
    {
        $this->blocRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn((new Bloc())
                ->setContent('TEST {type} ok')
            )
        ;

        $this->userRepository->expects($this->never())->method('findOneBy');
        $this->security->expects($this->never())->method('getUser');
        $this->translator->expects($this->once())->method('trans');
        $this->notificationManager->expects($this->once())->method('createNotification')->willReturnCallback(function(?User $sender = null, ?User $receiver = null, string $blocKey = null, array $params = []){
            $this->assertIsArray($params);
            $this->assertInstanceOf(User::class, $receiver);
            $this->assertNull($sender);

            return new Notification();
        });

        $media = (new Media())
            ->setStudent(
                (new Student())
                    ->setUser(
                        (new User())
                    )
            )
        ;

        $notification = $this->notificationMedia->generateRejectedNotification($media, 'subject', 'content'); 
        $this->assertInstanceOf(Notification::class, $notification);
    }

    public function testGenerateTransferredNotificationRecieverNotFoundGetAnError(): void
    {
        $this->blocRepository->expects($this->never())->method('findOneBy');
        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn(null);
        $this->security->expects($this->never())->method('getUser');
        $this->translator->expects($this->never())->method('trans');
        $this->notificationManager->expects($this->never())->method('createNotification');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Receiver not found');

        $this->notificationMedia->generateTransferredNotification('subject', 'content', '3', (new Media())
            ->setCode(MediaCodeConstants::CODE_BAC)
            ->setStudent((new Student)
                ->setIdentifier('TEST')
            )
        );
    }

    public function testGenerateTransferredNotificationStudentIdentifierNotFoundGetAnError(): void
    {
        $this->blocRepository->expects($this->never())->method('findOneBy');
        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn((new User()));
        $this->security->expects($this->never())->method('getUser');
        $this->translator->expects($this->never())->method('trans');
        $this->notificationManager->expects($this->never())->method('createNotification');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error transferred media: studentIdentifier does not have identifier');

        $this->notificationMedia->generateTransferredNotification('subject', 'content', '3', (new Media())
            ->setCode(MediaCodeConstants::CODE_BAC)
        );
    }

    public function testGenerateTransferredNotificationIsOk(): void
    {
        $this->blocRepository->expects($this->never())->method('findOneBy');
        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn((new User()));
        $this->security->expects($this->once())->method('getUser')->willReturn((new User()));
        $this->translator->expects($this->once())->method('trans')->willReturn('Baccalauréat');
        $this->notificationManager->expects($this->once())->method('createNotification')->willReturnCallback(function(?User $sender = null, ?User $receiver = null, string $blocKey = null, array $params = []){
            $this->assertIsArray($params);
            $this->assertInstanceOf(User::class, $receiver);
            $this->assertNotNull($sender);

            $this->assertSame('Transfert du document Baccalauréat pour la candidature TEST. content', $params['content']);
            return new Notification();
        });

        $notification = $this->notificationMedia->generateTransferredNotification('subject', 'content', '3', (new Media())
            ->setCode(MediaCodeConstants::CODE_BAC)
            ->setStudent((new Student)
                ->setIdentifier('TEST')
            )
        );

        $this->assertInstanceOf(Notification::class, $notification);
    }
}