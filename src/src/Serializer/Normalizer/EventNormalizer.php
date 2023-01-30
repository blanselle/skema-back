<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Constants\Event\EventConstants;
use App\Entity\Event;
use DateTime;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class EventNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    public function __construct(private NormalizerInterface $decorated)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if ($data instanceof Event) {
            return true;
        }

        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|object|null
    {
        if (!$object instanceof Event) {
            return $this->decorated->normalize($object, $format, $context);
        }

        $this->manageStatus($object);

        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsDenormalization($data, $type, $context = null): bool
    {
        return $this->decorated->supportsDenormalization($data, $type, $context);
    }

    public function denormalize($data, $class, $format = null, array $context = []): array|string|int|float|bool|object|null
    {
        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }

    private function manageStatus(Event $event)
    {
        $now = new DateTime();
        $dateStart = $event->getParamStart()->getValueDateTime();
        $dateEnd = $event->getParamEnd()?->getValueDateTime();

        $status = EventConstants::STATUS_NEXT;
        if (null == $dateEnd && $now->format(EventConstants::DEFAULT_DATE_FORMAT) == $dateStart->format(EventConstants::DEFAULT_DATE_FORMAT) ||
            null != $dateEnd && $now >= $dateStart && $now <= $dateEnd
        ) {
            $status = EventConstants::STATUS_CURRENT;
        } elseif ($dateStart < $now) {
            $status = EventConstants::STATUS_PREVIOUS;
        }
        $event->setStatus($status);
    }
}
