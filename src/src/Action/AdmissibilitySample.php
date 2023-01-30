<?php

declare(strict_types=1);

namespace App\Action;

use App\Entity\Admissibility\Border;
use App\Entity\Exam\ExamClassification;
use App\Service\Admissibility\AdmissibilityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdmissibilitySample extends AbstractController
{
    #[Route('/api/admissibility/sample', name: 'app_admissibility_pdf', methods: ['GET'])]
    public function __invoke(EntityManagerInterface $em, AdmissibilityManager $admissibilityManager): Response
    {
        $examClassification = $em->getRepository(ExamClassification::class)->find(3);

        $html = "";
        $bornes1 = new Border();
        $bornes1->setScore(50)
            ->setNote(12)
        ;

        $bornes2 = new Border();
        $bornes2->setScore(60)
            ->setNote(16)
        ;

        $collection = new ArrayCollection();
        $collection->add($bornes1);
        $collection->add($bornes2);

        foreach ($examClassification->getProgramChannels() as $programChannel) {
            $result = $admissibilityManager->getNotes($examClassification, $programChannel, null, null, null, $collection);
            $html .= '<h2>'.$programChannel->getName().'</h2>';
            $html .= '<table border="1">';
            $html .= '<tr><td>Score possible</td><td>Score obtenu</td><td>centil</td><td>Note</td></tr>';

            foreach ($result as $row) {
                $html .= '<tr><td>'.$row['available_score'].'</td><td>'.$row['student_score'].'</td><td>'.$row['centil'].'</td><td>'.$row['note'].'</td></tr>';
            }

            $html .= '</table>';
        }



        return new Response($html);
    }
}
