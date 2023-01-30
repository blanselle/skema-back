<?php

declare(strict_types=1);

namespace App\Handler\WrittenTest;

use App\Entity\Exam\ExamStudent;
use App\Manager\WrittenTest\SummonManager;
use App\Message\WrittenTest\SummonMessage;
use App\Repository\Exam\ExamStudentRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SummonHandler implements MessageHandlerInterface
{
    public function __construct(
        private SummonManager $summonManager,
        private ExamStudentRepository $examStudentRepository,
    ) {
    }

    public function __invoke(SummonMessage $message): void
    {
        $examStudent = $this->examStudentRepository->find($message->getExamStudentId());

        if($examStudent === null) {
            throw new EntityNotFoundException(sprintf('%s (%d) is not found', ExamStudent::class, $message->getExamStudentId()));
        }

        $this->summonManager->sendSummon($examStudent);
    }
}
