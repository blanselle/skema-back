<?php

declare(strict_types=1);

namespace App\Service\Media;

use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use App\Entity\Student;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaSummonsPathGenerator extends MediaPathGenerator
{
    public function __construct(
        ParameterBagInterface $params,
        private SluggerInterface $slugger
    ) {
        parent::__construct(params: $params, slugger: $slugger);
    }

    public function getAbsoluteMediaSummonsPath(ExamClassification $examClassification, ExamSession $examSession, Student $student): string
    {
        return sprintf(
            '%s/%s',
            $this->getRootFolder(),
            $this->getRelativeMediaSummonsPath($examClassification, $examSession, $student)
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
