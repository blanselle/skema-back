<?php

declare(strict_types=1);

namespace App\Service\Exam;

use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Entity\Student;
use App\Service\Export\ExcelGenerator;
use App\Service\Export\PageModel;
use Doctrine\ORM\EntityManagerInterface;

class SessionExport
{
    public function __construct(
        private ExcelGenerator $excelGenerator,
        private EntityManagerInterface $em,
    ) {
    }

    public function export(ExamSession $session): string
    {
        $students = $this->em->getRepository(Student::class)->findSessionByIdInArray($session->getId());
        $salles = [];
        $emargement = [];
        $all = [];
        $number = 1;
        foreach ($students as $student) {
            if (!isset($salles[$student['room']])) {
                $salles[$student['room']] = [];
            }

            $emargement[$student['room']][] = [
                $number,
                $student['identifier'],
                $student['lastname'],
                $student['firstname'],
                '',
            ];

            $salles[$student['room']][] = [
                $number,
                $student['identifier'],
                $student['lastname'],
                $student['firstname'],
            ];

            $all[] = [
                $number,
                $student['identifier'],
                $student['lastname'],
                $student['firstname'],
                $student['room'],
            ];
            $number++;
        }

        $pages = [
            (new PageModel())
                ->setName('Liste affichage totale')
                ->setTitle($session->getExamClassification()->getName())
                ->setHeaders(['N° de place', 'Identifiant', 'Nom', 'Prenom', 'Salle'])
                ->setRows($all)
        ];

        foreach ($salles as $name => $salle) {
            $pages[] = (new PageModel())
                ->setName(sprintf('Salle %s Liste d\'affichage', $name))
                ->setTitle(sprintf("%s - %s", $session->getExamClassification()->getName(), $name))
                ->setHeaders(['N° de Place', 'Identifiant', 'Nom', 'Prenom'])
                ->setRows($salle)
            ;
        }

        foreach ($emargement as $name => $salle) {
            $pages[] = (new PageModel())
                ->setName(sprintf('Salle %s Liste emargement', $name))
                ->setTitle(sprintf("%s - %s", $session->getExamClassification()->getName(), $name))
                ->setHeaders(['N° de Place', 'Identifiant', 'Nom', 'Prenom', 'Signature             '])
                ->setRows($salle)
                ->setRowHeight(50)
            ;
        }

        return $this->excelGenerator->generate(pages: $pages);
    }

    public function exportOnline(?ExamSession $examSession, int $delay): string
    {
        $return = [];
        $pages = [];
        $examSessions = $this->em->getRepository(ExamSession::class)->getExamSessionsOnline(false, $examSession);
        $i = 0;
        /** @var ExamSession $examSessionItem */
        foreach ($examSessions as $examSessionItem) {
            /** @var ExamStudent $examStudent */
            foreach ($examSessionItem->getExamStudents() as $examStudent) {
                $return[$i]['firstName'] = $examStudent->getStudent()->getUser()->getFirstName();
                $return[$i]['lastName'] = $examStudent->getStudent()->getUser()->getLastName();
                $return[$i]['email'] = $examStudent->getStudent()->getUser()->getEmail();
                $return[$i]['phone'] = $examStudent->getStudent()->getPhone();
                $return[$i]['reference'] = '';
                $return[$i]['delay'] = $delay;
                $i++;
            }
            $pages = [
                (new PageModel())
                    ->setName($examSessionItem->getExamClassification()->getName())
                    ->setTitle($examSessionItem->getExamClassification()->getName())
                    ->setHeaders(['Prénom', 'Nom', 'E-mail', 'Téléphone', 'Référence', 'Délai'])
                    ->setRows($return)
            ];
        }

        return $this->excelGenerator->generate(pages: $pages);
    }
}
