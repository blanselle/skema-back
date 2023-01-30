<?php

namespace App\Service\Admissibility\Ranking;

use App\Entity\ProgramChannel;

class DataRecordAverageToExport extends AbstractDataToExport
{
    private const ROW_LABEL = '[%s,00;%s,00[';
    private const MIN_SCORE = 8;
    private const MAX_SCORE = 20;

    public function __construct() {}

    public function generate(array $data): array
    {
        $result = [];
        $header = ['Note comprise entre'];

        foreach ($data as $datum) {
            /** @var ProgramChannel $programChannel */
            $programChannel = $datum['programChannel'];
            $programChannelName = $programChannel->getName();
            array_push($header, $programChannelName, sprintf('%s %s', '%', $programChannelName));
            $notes = $this->initNotes();
            $nbOfCandidates = $datum['nbOfCandidates'];

            foreach ($datum['noteValues'] as $admissibilityGlobalNote) {
                $found = false;
                $min = self::MIN_SCORE;
                $max = self::MAX_SCORE;
                $current = self::MIN_SCORE;
                do {
                    if ((float)$admissibilityGlobalNote < (float)$min) {
                        $notes[sprintf(self::ROW_LABEL, 0, $min)]['nb_of_candidates'] += 1;
                        $notes[sprintf(self::ROW_LABEL, 0, $min)]['percent'] = number_format(($notes[sprintf(self::ROW_LABEL, 0, $min)]['nb_of_candidates'] / $nbOfCandidates) * 100, 2, ',');
                        $found = true;
                    }

                    if((float)$admissibilityGlobalNote >= (float)$min && (float)$admissibilityGlobalNote < (float)$current) {
                        $notes[sprintf(self::ROW_LABEL, $current - 1, $current)]['nb_of_candidates'] += 1;
                        $notes[sprintf(self::ROW_LABEL, $current - 1, $current)]['percent'] = number_format(($notes[sprintf(self::ROW_LABEL, $current - 1, $current)]['nb_of_candidates'] / $nbOfCandidates) * 100, 2, ',');
                        $found = true;
                    }

                    if ((float)$admissibilityGlobalNote === (float)$max) {
                        $notes[$max]['nb_of_candidates'] += 1;
                        $notes[$max]['percent'] = number_format(($notes[$max]['nb_of_candidates'] / $nbOfCandidates) * 100, 2, ',');
                        $found = true;
                    }

                    ++$current;
                } while($found === false && $current < ($max + 1));
            }

            $i = 0;
            foreach ($notes as $key => $value) {
                if (!isset($result[$i])) {
                    $result[$i] = [$key];
                }
                array_push($result[$i], $value['nb_of_candidates'], $value['percent']);
                ++$i;
            }
        }

        return array_merge([$header], $result);
    }

    private function initNotes(): array
    {
        $notes = [];
        for ($i=self::MIN_SCORE; $i<=self::MAX_SCORE; $i++) {
            if ($i === self::MIN_SCORE) {
                $notes[sprintf(self::ROW_LABEL, 0, $i)] = ['nb_of_candidates' => 0, 'percent' => 0];
            } else {
                $notes[sprintf(self::ROW_LABEL, $i - 1, $i)] = ['nb_of_candidates' => 0, 'percent' => 0];
            }
        }

        $notes['20'] = ['nb_of_candidates' => 0, 'percent' => 0];

        return $notes;
    }
}