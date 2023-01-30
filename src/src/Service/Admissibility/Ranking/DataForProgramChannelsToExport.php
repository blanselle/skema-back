<?php

namespace App\Service\Admissibility\Ranking;

use App\Repository\StudentRepository;

class DataForProgramChannelsToExport extends AbstractDataToExport implements DataToExportInterface
{
    public function __construct(private StudentRepository $studentRepository){}

    public function generate(array $coefficients, array $programChannels): array
    {
        $result = [];

        foreach ($programChannels as $programChannel) {
            // do not insert data if coefficients did not exist in database
            if (!$this->canExport(coefficients: $coefficients, programChannelPositionKey: $programChannel->getPositionKey())) {
                continue;
            }

            $students = $this->studentRepository->getValidStudentshipRanking($programChannel);
            $nbOfCandidates = count($students);
            $result[$programChannel->getPositionKey()]['students'] = $students;
            $result[$programChannel->getPositionKey()]['programChannel'] = $programChannel;

            $maxScore = 20 * $coefficients[$programChannel->getPositionKey()];
            $result[$programChannel->getPositionKey()]['data'] = [];
            do {
                $nbOfCandidatesWithNoteSup = count(array_filter($students, function ($student) use ($maxScore) {
                    return $student->getAdmissibilityGlobalScore() >= $maxScore;
                }));

                $result[$programChannel->getPositionKey()]['data'][] = [$maxScore, number_format((0 !== $coefficients[$programChannel->getPositionKey()])? $maxScore / $coefficients[$programChannel->getPositionKey()] : 0, 2, ','), $nbOfCandidatesWithNoteSup, number_format((0 !== $nbOfCandidates)? (float)($nbOfCandidatesWithNoteSup / $nbOfCandidates) * 100 : 0, 2, ',')];
                $maxScore--;
            } while ($maxScore >= 0);

            $result[$programChannel->getPositionKey()]['data'] = array_merge([['Points', 'Moyenne', 'Candidats', '%']], $result[$programChannel->getPositionKey()]['data']);
        }


        ksort($result, SORT_STRING);

        return $result;
    }
}