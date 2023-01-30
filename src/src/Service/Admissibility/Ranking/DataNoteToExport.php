<?php

namespace App\Service\Admissibility\Ranking;

use App\Entity\Student;
use App\Repository\StudentRepository;

class DataNoteToExport extends AbstractDataToExport implements DataToExportInterface
{
    private const RANKING_RESULT_KEY = 6;

    public function __construct(private StudentRepository $studentRepository) {}

    public function generate(array $coefficients, array $programChannels): array
    {
        $programChannel = $programChannels[0] ?? null;
        $header = [
            'No Candidat',
            'Nom',
            'Prénom',
            'Code postal',
            'Ville',
            'Points',
            'Rang',
            'Moyenne',
            'Typologie épreuve management retenue',
            'Score management retenue',
            'Note management retenue',
            'Typologie épreuve anglais retenue',
            'Score anglais retenue',
            'Note anglais retenue',
            'Note CV',
            'Diplôme (diplôme du dossier administratif)',
            'Filière (filière du dossier administratif)',
        ];

        $result = [];
        if ($this->canExport(coefficients: $coefficients, programChannelPositionKey: $programChannel->getPositionKey())) {
            $students = $this->studentRepository->getValidStudentshipRanking($programChannel, ['admissibilityGlobalNote' => 'DESC']);
            
            /** @var Student $student */
            foreach ($students as $student) {
                $studentEnglishNote = $student->getEnglishNoteUsed();
                $studentManagementNote = $student->getManagementNoteUsed();

                $result[] = [
                    $student->getIdentifier(),
                    $student->getUser()->getLastName(),
                    $student->getUser()->getFirstName(),
                    $student->getPostalCode(),
                    $student->getCity(),
                    number_format($student->getAdmissibilityGlobalScore(), 2, ','),
                    $student->getAdmissibilityRanking(),
                    number_format($student->getAdmissibilityGlobalNote(), 2, ','),
                    $studentManagementNote?->getExamSession()->getExamClassification()->getName(),
                    number_format($studentManagementNote?->getScore(), 2, ','),
                    number_format($studentManagementNote?->getAdmissibilityNote(), 2, ','),
                    $studentEnglishNote?->getExamSession()->getExamClassification()->getName(),
                    number_format($studentEnglishNote?->getScore(), 2, ','),
                    number_format($studentEnglishNote?->getAdmissibilityNote(), 2, ','),
                    number_format($student->getGlobalCvNote(), 2, ','),
                    $student->getAdministrativeRecord()?->getStudentLastDiploma()?->getDiploma()?->getName(),
                    $student->getAdministrativeRecord()?->getStudentLastDiploma()?->getDiplomaChannel()?->getName(),
                ];
            }
        }

        usort($result, function($a, $b) {
            if ($a[self::RANKING_RESULT_KEY] == $b[self::RANKING_RESULT_KEY]) {
                return 0;
            }
            return ($a[self::RANKING_RESULT_KEY] < $b[self::RANKING_RESULT_KEY]) ? -1 : 1;
        });

        return array_merge([$header], $result);
    }
}