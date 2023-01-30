<?php

namespace App\DataPersister\ApiPlatform;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\User;
use App\Repository\MediaRepository;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class AdministrativeRecordPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private MediaWorkflowManager $mediaWorkflowManager,
        private MediaRepository $mediaRepository,
    ) {}

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AdministrativeRecord;
    }

    public function persist($data, array $context = []): object
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $medias = $this->mediaRepository->findOrphanJdcMedia($currentUser->getStudent());

        foreach($medias as $media) {
            /** @var AdministrativeRecord $data */
            if($media !== $data->getJdc()) {
                $this->mediaWorkflowManager->toCancel($media);
            }
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    public function remove($data, array $context = []): void
    {
    }
}