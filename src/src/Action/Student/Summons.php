<?php

declare(strict_types=1);

namespace App\Action\Student;

use App\Entity\Exam\ExamSummon;
use App\Entity\User;
use App\Manager\StudentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class Summons extends AbstractController
{
    public function __invoke(
        Security $security,
        EntityManagerInterface $entityManager,
        StudentManager $studentManager
    ): array {
        /** @var User $user */
        $user = $this->getUser();
        $student = $user->getStudent();
        
        $return = [];
        /** @var ExamSummon $summon */
        foreach ($studentManager->getSummons($student) as $summon) {
            $return[] = [$summon->getExamSession()->getExamClassification()->getName() => $summon->getMedia()];
        }

        return $return;
    }
}
