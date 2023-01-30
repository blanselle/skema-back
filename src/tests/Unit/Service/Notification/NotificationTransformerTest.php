<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Notification;

use App\Entity\Notification\Notification;
use App\Entity\Notification\NotificationTemplate;
use App\Entity\ProgramChannel;
use App\Entity\User;
use App\Repository\Notification\NotificationRepository;
use App\Repository\Notification\NotificationTemplateRepository;
use App\Repository\ProgramChannelRepository;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class NotificationTransformerTest extends TestCase
{
    private EntityManagerInterface|MockObject $em;
    private NotificationRepository|MockObject $notificationRepository;
    private UserRepository|MockObject $userRepository;
    private ProgramChannelRepository|MockObject $programChannelRepository;
    private NotificationTemplateRepository|MockObject $notificationTemplateRepository;

    private NotificationTransformer $notificationTransformer; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->notificationRepository = $this->createMock(NotificationRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->programChannelRepository = $this->createMock(ProgramChannelRepository::class);
        $this->notificationTemplateRepository = $this->createMock(NotificationTemplateRepository::class);

        $this->em->expects($this->any())->method('getRepository')->willReturnCallBack(function ($class) {
            
            return match($class) {
                Notification::class => $this->notificationRepository,
                User::class => $this->userRepository,
                ProgramChannel::class => $this->programChannelRepository,
                NotificationTemplate::class => $this->notificationTemplateRepository,
            };
        });

        $this->notificationTransformer = new NotificationTransformer($this->em);
    }

    public function testTransformIsOk(): void
    {
        $notification = $this->notificationProvider($this->blankNotificationProvider());

        $codes = ['a', 'b'];

        $notificationMessage = $this->notificationTransformer->transform($notification, $codes, false);

        $this->assertSame($notification->getComment(), $notificationMessage->getComment());
        $this->assertSame($notification->getSubject(), $notificationMessage->getSubject());
        $this->assertSame($notification->getContent(), $notificationMessage->getContent());
        $this->assertSame($notification->getNotificationTemplate()->getId(), $notificationMessage->getNotificationTemplateId());
        $this->assertSame($notification->getParent()->getId(), $notificationMessage->getParentId());
        $this->assertSame($notification->getReceiver()->getId()->__toString(), $notificationMessage->getReceiverId()->__toString());
        
        $this->assertSame($codes, $notificationMessage->getCodes());

        $this->assertSame(count($notification->getProgramChannels()), count($notificationMessage->getProgramChannelsIds()));
    }

    public function testTransformWithNullValuesIsOk(): void
    {
        $notification = $this->blankNotificationProvider(null);

        $codes = [];

        $notificationMessage = $this->notificationTransformer->transform($notification, $codes, false);

        $this->assertNull($notificationMessage->getComment());
        $this->assertNull($notificationMessage->getSubject());
        $this->assertNull($notificationMessage->getContent());
        $this->assertNull($notificationMessage->getNotificationTemplateId());
        $this->assertNull($notificationMessage->getParentId());
        $this->assertNull($notificationMessage->getReceiverId());

        $this->assertSame($codes, $notificationMessage->getCodes());

        $this->assertSame(count($notification->getProgramChannels()), count($notificationMessage->getProgramChannelsIds()));
    }

    public function testReverseIsOk(): void
    {
        $notification = $this->notificationProvider($this->notificationProvider());

        $codes = ['a', 'b'];

        $notificationMessage = $this->notificationTransformer->transform($notification, $codes, false);

        $this->notificationRepository->expects($this->once())->method('__call')->willReturn($this->notificationProvider(null));
        $this->userRepository->expects($this->exactly(2))->method('__call')->willReturn($this->userProvider());
        $this->programChannelRepository->expects($this->exactly(2))->method('__call')->willReturn($this->programChannelProvider());
        $this->notificationTemplateRepository->expects($this->once())->method('__call')->willReturn($this->notificationTemplateProvider());

        [$notification, $codes] = $this->notificationTransformer->reverse($notificationMessage);

        $this->assertSame($notification->getComment(), $notificationMessage->getComment());
        $this->assertSame($notification->getSubject(), $notificationMessage->getSubject());
        $this->assertSame($notification->getContent(), $notificationMessage->getContent());
        $this->assertSame($notification->getNotificationTemplate()->getId(), $notificationMessage->getNotificationTemplateId());
        $this->assertSame($notification->getParent()->getId(), $notificationMessage->getParentId());
        $this->assertSame($notification->getReceiver()->getId()->__toString(), $notificationMessage->getReceiverId()->__toString());
        
        $this->assertSame($codes, $notificationMessage->getCodes());

        $this->assertSame(count($notification->getProgramChannels()), count($notificationMessage->getProgramChannelsIds()));
    }

    public function testReverseWithNullValues(): void
    {
        $notification = $this->blankNotificationProvider(null);

        $codes = [];

        $notificationMessage = $this->notificationTransformer->transform($notification, $codes, false);

        $this->notificationRepository->expects($this->never())->method('__call')->willReturn(null);
        $this->userRepository->expects($this->once())->method('__call')->willReturn($this->userProvider());
        $this->programChannelRepository->expects($this->never())->method('__call')->willReturn($this->programChannelProvider());
        $this->notificationTemplateRepository->expects($this->never())->method('__call')->willReturn($this->notificationTemplateProvider());

        [$notification, $codes] = $this->notificationTransformer->reverse($notificationMessage);

        $this->assertNull($notification->getComment());
        $this->assertNull($notification->getSubject());
        $this->assertNull($notification->getContent());
        $this->assertNull($notification->getNotificationTemplate());
        $this->assertNull($notification->getParent());
        $this->assertNull($notification->getReceiver());

        $this->assertSame($codes, $notificationMessage->getCodes());

        $this->assertSame(count($notification->getProgramChannels()), count($notificationMessage->getProgramChannelsIds()));
    }

    private function userProvider(): User
    {
        return (new User())
            ->setId(new Uuid('1ecf2d23-8108-69a2-9e18-2fcb73c9d899'))
        ;
    }

    private function notificationProvider(?Notification $parent = null): Notification
    {
        return (new Notification())
            ->setId(1)->setSender($this->userProvider())
            ->setParent($parent)
            ->setReceiver($this->userProvider())
            ->setNotificationTemplate($this->notificationTemplateProvider())
            ->setProgramChannels(new ArrayCollection([
                $this->programChannelProvider(),
                $this->programChannelProvider(),
            ]))
            ->setSubject('Subject')
            ->setComment('Content')
            ->setComment('Comment')
            ->setIdentifier('Identifier')
            ->setRoles(['ROLE1', 'ROLE2'])
        ;
    }

    private function blankNotificationProvider(?Notification $parent = null): Notification
    {
        return (new Notification())
            ->setSender($this->userProvider())
            ->setId(1)
        ;
    }

    private function programChannelProvider(): ProgramChannel
    {
        return (new ProgramChannel())
            ->setId(1)
        ;
    }

    private function notificationTemplateProvider(): NotificationTemplate
    {
        return (new NotificationTemplate())
            ->setId(1)
        ;
    }
}