<?php

declare(strict_types=1);

namespace App\Action\Cv;

use App\Entity\CV\Cv;
use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validation extends AbstractController
{
    public function __invoke(
        Cv $cv,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ): Cv|Response {

        /** @var User $user */
        $user = $this->getUser();

        if ($user->getStudent()->getCv() !== $cv) {
            throw new BadRequestException('Access denied');
        }

        $errors = $validator->validate($cv, groups: 'cv:validation');

        if (0 !== count($errors)) {
            return new JsonResponse(
                (new ValidationException($errors))->getMessages(),
                Response::HTTP_BAD_REQUEST
            );
        }

        $cv->setValidated(true);

        $em->flush();

        return $cv;
    }
}
