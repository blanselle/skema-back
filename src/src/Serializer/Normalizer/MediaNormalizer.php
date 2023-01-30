<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Constants\Media\MediaCodeConstants;
use App\Entity\Media;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MediaNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    public function __construct(
        private NormalizerInterface $decorated,
        private UrlGeneratorInterface $router,
        private string $backofficeUrl
    ) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if ($data instanceof Media) {
            return true;
        }

        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|object|null
    {
        /** @var array $data */
        $data = $this->decorated->normalize($object, $format, $context);

        if ($object instanceof Media && array_key_exists('file', $data) && !empty($data['file'])) {
            $object->setFile(sprintf(
                '%s/%s',
                rtrim($this->backofficeUrl, '/'),
                ltrim($this->router->generate('media_rendering', ['id' => $object->getId()]), '/')
            ));
        }

        if($object instanceof Media && in_array($object->getCode(), MediaCodeConstants::getBulletins(), true)) {
            $object->setCode(str_replace('bulletin_', '', $object->getCode()));
        }

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
}
