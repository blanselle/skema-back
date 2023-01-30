<?php

namespace App\Action\OralTest;

use App\Entity\OralTest\CampusOralDay;
use App\Entity\User;
use App\Exception\Parameter\ParameterKeyNotFoundException;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Service\OralTest\CampusOralDayManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class GetCollectionAvailable extends AbstractController
{
    public function __construct(private CampusOralDayManager $campusOralDayManager, private Security $security) {}

    /**
     * @return CampusOralDay[]
     * @throws ParameterKeyNotFoundException
     * @throws ParameterNotFoundException
     */
    public function __invoke()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $student = $user->getStudent();
        if (null === $student) {
            throw new Exception('Student not found');
        }

        return $this->campusOralDayManager->getAvailableSlots(student: $student);
    }
}