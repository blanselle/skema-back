<?php

declare(strict_types=1);

namespace App\Handler\WrittenTest;

use App\Entity\Exam\ExamClassification;
use App\Entity\User;
use App\Manager\WrittenTest\SummonManager;
use App\Message\WrittenTest\SummonsMessage;
use App\Repository\Exam\ExamClassificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SummonsHandler implements MessageHandlerInterface
{
    public function __construct(
        private SummonManager $summonManager,
        private ExamClassificationRepository $examClassificationRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(SummonsMessage $message): void
    {
        $examClassification = $this->examClassificationRepository->find($message->getExamClassificationId());
        $user = $this->userRepository->find($message->getUserId());

        if($examClassification === null) {
            throw new EntityNotFoundException(sprintf('%s (%d) is not found', ExamClassification::class, $message->getExamClassificationId()));
        }

        if($user === null) {
            throw new EntityNotFoundException(sprintf('%s (%d) is not found', User::class, $message->getUserId()));
        }

        $this->summonManager->sendSummons(examClassification: $examClassification, user: $user);
    }
}
