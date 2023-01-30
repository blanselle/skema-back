<?php

declare(strict_types=1);

namespace App\Service\Media;

use App\Constants\Media\MediaPathConstants;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use App\Entity\Media;
use App\Entity\Student;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaPathGenerator
{
    public function __construct(
        private ParameterBagInterface $params,
        private SluggerInterface $slugger
    ) {
    }

    public function getRootFolder(): string
    {
        return strval($this->params->get(MediaPathConstants::ROOT_PATH));
    }

    public function getRelativePrivateFolder(): string
    {
        return strval($this->params->get(MediaPathConstants::PRIVATE_PATH));
    }

    public function getRelativeFixturesFolder(): string
    {
        return strval($this->params->get(MediaPathConstants::FIXTURE_PATH));
    }

    public function getAbsolutePrivateFolder(): string
    {
        return sprintf(
            '%s/%s',
            $this->getRootFolder(),
            $this->getRelativePrivateFolder()
        );
    }

    public function getAbsoluteFixturesFolder(): string
    {
        return sprintf(
            '%s/%s',
            $this->getRootFolder(),
            $this->getRelativeFixturesFolder()
        );
    }

    public function getRelativePathFromFileName(string $fileName): string
    {
        return sprintf(
            '%s/%s',
            $this->getRelativePrivateFolder(),
            $fileName,
        );
    }

    public function getAbsolutePathOfMedia(Media $media): string
    {
        return sprintf(
            '%s/%s',
            $this->getRootFolder(),
            $media->getFile(),
        );
    }

    public function getAbsoluteMediaSummonsPath(ExamClassification $examClassification, ExamSession $examSession, Student $student): string
    {
        $directory = strtolower((string)$this->slugger->slug($examClassification->getName()));
        return sprintf(
            '%s/%s/summons/%s/%s_%d_%s.pdf',
            $this->getRootFolder(),
            $this->getRelativePrivateFolder(),
            $directory,
            $directory,
            $examSession->getId(),
            $student->getIdentifier()
        );
    }

    public function getRelativeMediaSummonsPath(ExamClassification $examClassification, ExamSession $examSession, Student $student): string
    {
        $directory = strtolower((string)$this->slugger->slug($examClassification->getName()));
        return sprintf(
            '%s/summons/%s/%s_%d_%s.pdf',
            $this->getRelativePrivateFolder(),
            $directory,
            $directory,
            $examSession->getId(),
            $student->getIdentifier()
        );
    }
}
