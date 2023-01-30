<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\Diploma\StudentDiploma;
use App\Manager\StudentManager;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AdministrativeRecordNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    public function __construct(
        private NormalizerInterface $decorated,
        private StudentManager $studentManager
    ) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if ($data instanceof AdministrativeRecord) {
            return true;
        }

        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|object|null
    {
        if (!$object instanceof AdministrativeRecord) {
            return $this->decorated->normalize($object, $format, $context);
        }

        $this->removeDualPathStudentDiplomasFromRoot($object);
        $object->setStudentLastDiploma($this->studentManager->getStudentLastDiploma($object->getStudent()));

        return $this->decorated->normalize($object, $format, $context);
    }

    public function supportsDenormalization($data, $type, $context = []): bool
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

    private function removeDualPathStudentDiplomasFromRoot(AdministrativeRecord $administrativeRecord): void
    {
        $dualPathStudentDiplomas = [];
        /** @var StudentDiploma $studentDiploma */
        foreach ($administrativeRecord->getStudentDiplomas() as $studentDiploma) {
            $dualPathStudentDiplomas[] = $studentDiploma->getDualPathStudentDiploma();
        }

        foreach ($dualPathStudentDiplomas as $dualPathStudentDiploma) {
            if ($administrativeRecord->getStudentDiplomas()->contains($dualPathStudentDiploma)) {
                $administrativeRecord->getStudentDiplomas()->removeElement($dualPathStudentDiploma);
            }

            // @see https://stackoverflow.com/questions/62944819/symfony-4-4-serializer-problem-when-one-or-more-items-from-entitys-array-collec
            $administrativeRecord->setStudentDiplomas(
                new ArrayCollection($administrativeRecord->getStudentDiplomas()->getValues())
            );
        }
    }
}
