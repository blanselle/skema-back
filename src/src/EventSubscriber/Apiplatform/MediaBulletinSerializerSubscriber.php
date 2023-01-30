<?php

declare(strict_types=1);

namespace App\EventSubscriber\Apiplatform;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Action\Media\CreateMedia;
use App\Constants\Media\MediaCodeConstants;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Add prefix on bulletin media
 */
class MediaBulletinSerializerSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['preDeserialize', EventPriorities::PRE_DESERIALIZE],
        ];
    }

    public function preDeserialize(RequestEvent $event): void
    {
        if($event->getRequest()->attributes->get('_controller') !== CreateMedia::class) {
            return;
        }

        $code = $event->getRequest()->request->get('code');

        if(null === $code) {
            return;
        }

        $code = "bulletin_{$code}";

        if(!in_array($code, MediaCodeConstants::getBulletins(), true)){
            return;
        }
        
        $event->getRequest()->request->set('code', 'bulletin_' . $event->getRequest()->request->get('code'));
    }
}