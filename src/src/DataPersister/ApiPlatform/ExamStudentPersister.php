<?php

declare(strict_types=1);

namespace App\DataPersister\ApiPlatform;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Exam\ExamStudent;
use App\Repository\MediaRepository;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * https://pictime.atlassian.net/browse/SB-1201
 */
class ExamStudentPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private MediaWorkflowManager $mediaWorkflowManager,
        private MediaRepository $mediaRepository,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ExamStudent;
    }

    public function persist($examStudent, array $context = []): void
    {
        $medias = $this->mediaRepository->findOrphanExamStudentMedia($examStudent);

        foreach($medias as $media) {
            if($media !== $examStudent->getMedia()) {
                $this->mediaWorkflowManager->toCancel($media);
            }
        }

        $this->em->persist($examStudent);
        $this->em->flush();
    }

    public function remove($data, array $context = []): void
    {
    }
}
