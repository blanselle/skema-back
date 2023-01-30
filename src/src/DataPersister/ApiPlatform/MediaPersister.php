<?php

namespace App\DataPersister\ApiPlatform;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Media;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;

class MediaPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MediaWorkflowManager $mediaWorkflowManager
    ) {}

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Media;
    }

    public function persist($data, array $context = []): object
    {
        $this->manager->persist($data);
        $this->manager->flush();
        
        return $data;
    }

    public function remove($data, array $context = []): void
    {
        $this->mediaWorkflowManager->toCancel($data);
        $this->manager->flush();
    }
}