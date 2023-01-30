<?php

declare(strict_types=1);

namespace App\Action;

use App\Entity\Student;
use App\Ruler\CV\CvRuler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RulerSample extends AbstractController
{
    #[Route('/api/ruler/sample', name: 'app_sample_ruler', methods: ['GET'])]
    public function __invoke(EntityManagerInterface $em, CvRuler $cvRuler): Response
    {
        $student = $em->getRepository(Student::class)->find(1);

        $html = sprintf(
            'Bonification pour %s %s: %.2f',
            $student->getUser()->getFirstName(),
            $student->getUser()->getLastName(),
            $cvRuler->getBonus($student)
        );

        return new Response($html);
    }
}
