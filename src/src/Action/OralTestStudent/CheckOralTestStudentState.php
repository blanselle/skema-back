<?php

declare(strict_types=1);

namespace App\Action\OralTestStudent;

use App\Entity\OralTest\OralTestStudent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CheckOralTestStudentState extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/api/oral_test_students/{id}/check', name: 'api_check_oral_test_student_state', methods: ['GET'])]
    public function __invoke(int $id): JsonResponse
    {
        /** @var null|OralTestStudent $oralTestStudent */
        $oralTestStudent = $this->em->getRepository(OralTestStudent::class)->find($id);

        if (null === $oralTestStudent) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(['status' => $oralTestStudent->getState()]);
    }
}
