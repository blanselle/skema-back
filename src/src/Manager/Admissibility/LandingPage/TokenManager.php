<?php

declare(strict_types=1);

namespace App\Manager\Admissibility\LandingPage;

use App\Entity\Admissibility\LandingPage\AdmissibilityStudentToken;
use App\Entity\Student;
use App\Helper\TokenHelper;
use App\Repository\Admissibility\LandingPage\AdmissibilityStudentTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class TokenManager
{
    public function __construct(
        private EntityManagerInterface $em, 
        private AdmissibilityStudentTokenRepository $admissibilityStudentTokenRepository,
    ) {}

    public function saveToken(Student $student): AdmissibilityStudentToken
    {
        $admissibilityStudentToken = $this->admissibilityStudentTokenRepository->findOneBy(['student' => $student]);

        if(null === $admissibilityStudentToken) {
            $admissibilityStudentToken = (new AdmissibilityStudentToken())
                ->setStudent($student)
            ;

            $this->em->persist($admissibilityStudentToken);
        }
        
        $admissibilityStudentToken->setToken(TokenHelper::createToken());

        $this->em->flush();

        return $admissibilityStudentToken;
    }
}