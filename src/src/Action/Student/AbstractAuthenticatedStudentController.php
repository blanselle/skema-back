<?php

declare(strict_types=1);

namespace App\Action\Student;

use App\Entity\Student;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

abstract class AbstractAuthenticatedStudentController extends AbstractController
{
    protected function checkAuthenticatedStudent(Student $student): void 
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null === $user->getStudent()) {
            throw new BadRequestException('You have to be a candidate to perform this action');
        }

        if ($student->getUser() !== $user) {
            throw new BadRequestException('You have to be the candidate to perform this action');
        }
    }
}
