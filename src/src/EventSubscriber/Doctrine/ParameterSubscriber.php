<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine;

use App\Constants\Parameters\ParametersKeyTypeConstants;
use App\Entity\Parameter\Parameter;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ParameterSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad => 'postLoad',
        ];
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Parameter) {
            return;
        }

        $this->rewriteValue($entity);
    }

    private function rewriteValue(Parameter $parameter): Parameter
    {
        if ($parameter->getKey()->getType() === ParametersKeyTypeConstants::DATE) {
            $parameter->setValue($parameter->getValueDateTime());
        }

        if ($parameter->getKey()->getType() === ParametersKeyTypeConstants::TEXT) {
            $parameter->setValue($parameter->getValueString());
        }

        if ($parameter->getKey()->getType() === ParametersKeyTypeConstants::NUMBER) {
            $parameter->setValue($parameter->getValueNumber());
        }
        return $parameter;
    }
}
