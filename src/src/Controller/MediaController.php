<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\Media\MediaCodeConstants;
use App\Entity\Media;
use App\Entity\User;
use App\Service\Media\MediaPathGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @method User getUser()
 */
class MediaController extends AbstractController
{
    #[Route('/api/media/{id}/render', name: 'media_rendering', methods: ['GET'])]
    #[Route('/admin/media/{id}/render', name: 'media_rendering_admin', methods: ['GET'])]
    public function renderMedia(Media $media, MediaPathGenerator $mediaPathGenerator, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (in_array($media->getCode(), [MediaCodeConstants::CODE_AUTRE, MediaCodeConstants::CODE_SUMMON], true)) {
            return new BinaryFileResponse(
                $mediaPathGenerator->getAbsolutePathOfMedia($media)
            );
        }

        if (!$authorizationChecker->isGranted('ROLE_COORDINATOR', $this->getUser()) &&
            null === $this->getUser()->getStudent() &&
            $media->getStudent() !== $this->getUser()->getStudent()
        ) {
            throw new AccessDeniedException();
        }

        return new BinaryFileResponse(
            $mediaPathGenerator->getAbsolutePathOfMedia($media)
        );
    }
}
