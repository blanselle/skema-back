<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Bloc\Bloc;
use App\Entity\ProgramChannel;
use App\Repository\ProgramChannelRepository;
use App\Service\Bloc\BlocRewriter;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BlocNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    public function __construct(
        private NormalizerInterface $decorated,
        private RequestStack $requestStack,
        private ProgramChannelRepository $programChannelRepository,
        private BlocRewriter $blocRewriter,
    ) {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if ($data instanceof Bloc) {
            return true;
        }

        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = []): array|string|int|float|bool|object|null
    {
        if (!$object instanceof Bloc) {
            return $this->decorated->normalize($object, $format, $context);
        }

        $programChannel = $this->getProgramChannel();

        $rewritedBloc = $this->blocRewriter->rewriteBloc($object, $programChannel);

        return $this->decorated->normalize($rewritedBloc, $format, $context);
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

    private function getProgramChannel(): ?ProgramChannel
    {
        $programChannelId = null;
        $programChannels = $this->requestStack->getMainRequest()->get('programChannels');
        if (null !== $programChannels) {
            $programChannelId = end($programChannels);
        }

        if (null === $programChannelId) {
            return null;
        }

        $programChannel = $this->programChannelRepository->find($programChannelId);

        if (null === $programChannel) {
            throw new NotFoundHttpException("The programChannel not found");
        }

        return $programChannel;
    }
}
