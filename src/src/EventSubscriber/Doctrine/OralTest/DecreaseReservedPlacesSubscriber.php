<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine\OralTest;

use App\Entity\OralTest\OralTestStudent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\Events;
use ApiPlatform\HttpCache\VarnishPurger;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class DecreaseReservedPlacesSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private VarnishPurger $purger,
        private LoggerInterface $logger,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            Events::postRemove => 'postRemove',
        ];
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $oralTestStudent = $args->getObject();
        
        if (!$oralTestStudent instanceof OralTestStudent) {
            return;
        }

        $nbPlaces = $oralTestStudent->getCampusOralDay()->getNbOfReservedPlaces() - 1;
        if($nbPlaces < 0){
            $this->logger->critical(sprintf('postRemove OralTestStudent %s invalid nbplaces', $oralTestStudent->getId()));
            $nbPlaces = 0;
        }

        $oralTestStudent->getCampusOralDay()->setNbOfReservedPlaces($nbPlaces);

        $this->purger->purge([sprintf('/api/oral_test_students/%d/check', $oralTestStudent->getId())]);
    }
}
