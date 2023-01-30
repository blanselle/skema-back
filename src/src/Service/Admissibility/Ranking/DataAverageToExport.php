<?php

namespace App\Service\Admissibility\Ranking;

use App\Repository\StudentRepository;

class DataAverageToExport extends AbstractDataToExport implements DataToExportInterface
{
    public function __construct(private StudentRepository $studentRepository) {}

    public function generate(array $coefficients, array $programChannels): array
    {
        $result = [];
        foreach ($programChannels as $programChannel) {
            // do not insert data if coefficients did not exist in database
            if (!$this->canExport(coefficients: $coefficients, programChannelPositionKey: $programChannel->getPositionKey())) {
                continue;
            }

            list('notes' => $notes, 'nbOfCandidates' => $nbOfCandidates, 'noteValues' => $noteValues) = $this->studentRepository->getNotesForRankingExport($programChannel);

            $data = array_map(function($v) {
                $total = null;
                if ($v['total_candidates'] !== 0) {
                    $total = number_format($v['sum_of_notes'] / $v['total_candidates'], 2, ',');
                }
                return [$v['name'], $total, $v['total_candidates']];
            }, $notes);

            $output = array_merge([['Epreuve', 'Moyennes', 'Candidats']], $data);

            $result[$programChannel->getPositionKey()] = [
                'notes' => $notes,
                'nbOfCandidates' => $nbOfCandidates,
                'data' => $output,
                'noteValues' => $noteValues,
                'programChannel' => $programChannel,
            ];
        }

        return $result;
    }
}