<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\CV\Cv;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CvNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    public function __construct(
        private NormalizerInterface $decorated
    ) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if ($data instanceof Cv) {
            return true;
        }

        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|object|null
    {
        if (!$object instanceof Cv) {
            return $this->decorated->normalize($object, $format, $context);
        }

        $this->removeDualPathBacSupsFromRoot($object);

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

    private function removeDualPathBacSupsFromRoot(Cv $cv): void
    {
        $dualPathBackSups = [];
        foreach ($cv->getBacSups() as $bacSup) {
            $dualPathBackSups[] = $bacSup->getDualPathBacSup();
        }

        foreach ($dualPathBackSups as $dualPathBackSup) {
            if ($cv->getBacSups()->contains($dualPathBackSup)) {
                $cv->getBacSups()->removeElement($dualPathBackSup);
            }
        }

        // @see https://stackoverflow.com/questions/62944819/symfony-4-4-serializer-problem-when-one-or-more-items-from-entitys-array-collec
        $cv->setBacSups(new ArrayCollection($cv->getBacSups()->getValues()));
    }
}
