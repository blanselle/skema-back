<?php

declare(strict_types=1);

namespace App\Service\Admissibility;

use App\Entity\Student;
use App\Service\Export\ExcelGenerator;
use App\Service\Export\PageModel;
use Exception;

class AdmissibilityExportManager
{
    public function __construct(private ExcelGenerator $excelGenerator) {}

    public function exportAdmissible(array $admissibles): string
    {
        $pages = [];
        $export = [];
        foreach ($admissibles as $students) {
            /** @var Student $student */
            foreach ($students as $student) {

                if($student->getProgramChannel() === null) {
                    throw new Exception(sprintf('L\'étudiant %sn\'a pas de voie de concours', $student->getIdentifier()));
                }

                if(!isset($export[$student->getProgramChannel()->getPositionKey()])) {
                    $export[$student->getProgramChannel()->getPositionKey()] = [
                        'programChannel' => $student->getProgramChannel(),
                        'students' => [],
                    ];
                }
                $export[$student->getProgramChannel()->getPositionKey()]['students'][] = [
                    $student->getIdentifier(),
                    $student->getUser()->getLastName(),
                    $student->getUser()->getFirstName(),
                    $student->getPostalCode(),
                    $student->getCity(),
                    $student->getAdmissibilityGlobalScore(),
                    $student->getAdmissibilityRanking(),
                    $student->getAdmissibilityGlobalNote(),
                ];

                ksort($export, SORT_STRING);
            }
        }

        foreach ($export as $positionKey => $item) {
            $pages[] = (new PageModel())
                    ->setName($item['programChannel']->getName())
                    ->setTitle($item['programChannel']->getName())
                    ->setHeaders(['Identifiant', 'Nom', 'Prenom', 'Code postal', 'Ville', 'Score', 'Rang', 'Note global'])
                    ->setRows($item['students'])
            ;
        }

        return $this->excelGenerator->generate(pages: $pages);
    }
}