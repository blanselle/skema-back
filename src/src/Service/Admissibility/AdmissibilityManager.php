<?php

declare(strict_types=1);

namespace App\Service\Admissibility;

use App\Entity\Admissibility\Border;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamClassificationScore;
use App\Entity\ProgramChannel;
use App\Repository\StudentRepository;
use DivisionByZeroError;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class AdmissibilityManager
{
    public function __construct(private EntityManagerInterface $em, private StudentRepository $studentRepository) {}

    // return array of associative array
    private function getPercentilesForExamClassification(ExamClassification $examClassification, ProgramChannel $programChannel, float $scoreMin = null, float $scoreMax = null): array
    {
        $parameters = [
            'exam_classification_id' => $examClassification->getId(),
            'program_channel_id' => $programChannel->getId()
        ];

        $sql = '
            select
                ecs.score as available_score,
                tmp.score as student_score,
                COUNT(tmp.score) as nb_student,
                tmp.centil
            from exam_classification_score ecs
            left join (
                select
                    es.score,
                    percent_rank() over (order by es.score) as centil
                    from exam_student es
                    join student s on s.id = es.student_id
                    where 
                          s.program_channel_id = :program_channel_id 
                          and s.state = \'approved\'
                          and es.exam_session_id in (select id from exam_session where exam_classification_id = :exam_classification_id)  ';

        if (null !== $scoreMin) {
            $sql .= ' and score >= :score_min';
            $parameters['score_min'] = $scoreMin;
        }

        if (null !== $scoreMax && $scoreMax != $examClassification->getExamClassificationScores()->get($examClassification->getExamClassificationScores()->count() - 1)->getScore()) {
            $sql .= ' and score <= :score_max';
            $parameters['score_max'] = $scoreMax;
        }

        if (null !== $scoreMax && $scoreMax == $examClassification->getExamClassificationScores()->get($examClassification->getExamClassificationScores()->count() - 1)->getScore()) {
            $sql .= ' and score <= :score_max';
            $parameters['score_max'] = $scoreMax;
        }

        $sql .= '
                    order by score
                ) tmp on tmp.score = ecs.score
            where exam_classification_id = :exam_classification_id';

        if (null !== $scoreMin) {
            $sql .= ' and ecs.score >= :score_min';
            $parameters['score_min'] = $scoreMin;
        }

        if (null !== $scoreMax && $scoreMax != $examClassification->getExamClassificationScores()->get($examClassification->getExamClassificationScores()->count() - 1)->getScore()) {
            $sql .= ' and ecs.score <= :score_max';
            $parameters['score_max'] = $scoreMax;
        }

        if (null !== $scoreMax && $scoreMax == $examClassification->getExamClassificationScores()->get($examClassification->getExamClassificationScores()->count() - 1)->getScore()) {
            $sql .= ' and ecs.score <= :score_max';
            $parameters['score_max'] = $scoreMax;
        }

        $sql .= ' GROUP BY ecs.score, tmp.score, tmp.centil ORDER BY ecs.score ASC';

        $statement = $this->em->getConnection()->prepare($sql);

        return $statement
            ->executeQuery($parameters)
            ->fetchAllAssociative();
    }

    private function fillAllCentiles(array $percentiles): array
    {
        $borneMin = null;
        $borneMax = null;
        $intermediateIndexes = [];
        $previousCentil = null;
        $countPercentiles = count($percentiles);
        for ($i = 0; $i < $countPercentiles; $i++) {

            // Check case of no studentScore for minimum available score
            /** @phpstan-ignore-next-line */
            if ($percentiles[$i]['centil'] === null && $borneMin === null && $borneMax === null) {
                $borneMin = $previousCentil;
                $intermediateIndexes[] = $i;
            }

            // fill intermediate index between empty student score
            /** @phpstan-ignore-next-line */
            if ($borneMin !== null && $borneMax === null) {
                $intermediateIndexes[] = $i;
            }

            // borne max found
            if ($percentiles[$i]['centil'] !== null && !empty($intermediateIndexes)) {
                $borneMax = $percentiles[$i]['centil'];
            }

            // fill intermediate centil without value
            if ($borneMax !== null) {
                if ($borneMin === null) {
                    foreach ($intermediateIndexes as $index) {
                        $percentiles[$index]['centil'] = 0;
                    }
                }

                if ($borneMin !== null && $percentiles[$i]['centil'] !== null) {
                    $ecart = $borneMax - $borneMin;
                    $countIntermediateIndexes = count($intermediateIndexes);
                    $pas = $ecart / $countIntermediateIndexes;

                    for ($c=0; $c < $countIntermediateIndexes;$c++) {
                        $percentiles[$intermediateIndexes[$c]]['centil'] = round($borneMin + (($c+1) * $pas), 6);
                    }
                }

                $intermediateIndexes = [];
                $borneMin = null;
                $borneMax = null;
            }

            // fill last centil without student score
            /** @phpstan-ignore-next-line */
            if ($i === count($percentiles) - 1 && $borneMax === null) {
                foreach ($intermediateIndexes as $index) {
                    $percentiles[$index]['centil'] = 1;
                }
            }
            /** @phpstan-ignore-next-line */
            if ($i === count($percentiles) - 1 && ($borneMax === null && $borneMin === null)) {
                foreach ($intermediateIndexes as $index) {
                    $percentiles[$index]['centil'] = 0;
                }
            }

            $previousCentil = $percentiles[$i]['centil'];
        }

        // refill centil to avoid duplicate centil
        $previousCentil = null;
        $sameCentilIndexes = [];
        for($i = 0; $i < count($percentiles); $i++) {
            if (null === $previousCentil) {
                $sameCentilIndexes[] = $i;
                $previousCentil = $percentiles[$i]['centil'];
                continue;
            }

            if ($previousCentil == $percentiles[$i]['centil']) {
                $sameCentilIndexes[] = $i;
            } else {
                if (1 == count($sameCentilIndexes)) {
                    continue;
                } else {
                    $avg = ($percentiles[$sameCentilIndexes[0]]['centil'] + $percentiles[$i]['centil']) / (count($sameCentilIndexes));
                    for($j = 0; $j < count($sameCentilIndexes); $j++) {
                        $percentiles[$sameCentilIndexes[$j]]['centil'] = $percentiles[$sameCentilIndexes[$j]]['centil'] + $avg * $j;
                    }

                    $sameCentilIndexes = [$i];
                }
            }

            $previousCentil = $percentiles[$i]['centil'];
        }

        return $percentiles;
    }

    private function setNotes(
        ExamClassification $examClassification,
        ProgramChannel $programChannel,
        float $medianNote = null,
        float $scoreMin = null,
        float $scoreMax = null,
        float $noteMin = null,
        float $noteMax = null
    ): array {
        $percentiles = $this->getPercentilesForExamClassification($examClassification, $programChannel, $scoreMin, $scoreMax);
        $filledPercentiles = $this->fillAllCentiles($percentiles);

        $tmpNoteMax = $noteMax;
        $tmpNoteMin = $noteMin;

        try {
            $ratio = ($noteMax - $noteMin) / ($scoreMax - $scoreMin);
        } catch (DivisionByZeroError $e) {
            $ratio = 0;
        }

        $betweenBorn = $noteMax - $noteMin;
        $diff = $betweenBorn - $ratio;

        $countFilledPercentiles = count($filledPercentiles);

        // set others notes
        for ($i = 0; $i < $countFilledPercentiles; $i++) {
            $note = $diff * $filledPercentiles[$i]['centil'] + $tmpNoteMin;
            if ($i == 0) {
                $note = $noteMin;
            }

            if (1 == $filledPercentiles[$i]['centil'] && 20 == $noteMax) {
                $note = $noteMax;
            }

            $filledPercentiles[$i]['note'] = $note;
        }

        return $filledPercentiles;
    }

    public function getNotes(
        ExamClassification $examClassification,
        ProgramChannel $programChannel,
        float $medianNote = null,
        int $lowClipping = null,
        int $highClipping = null,
        Collection $fixedBornes = null,
    ): array {
        $availableScores = $this->em->getRepository(ExamClassificationScore::class)->findBy(
            ['examClassification' => $examClassification],
            ['score' => 'asc']
        );

        $scoreMax = $availableScores[count($availableScores) - 1]->getScore();
        $scoreMin = $availableScores[0]->getScore();

        $noteMin = 0.01;
        $noteMax = 20;

        $subset = [];
        $subset[] = [
            'noteMin' => $noteMin,
            'noteMax' => $noteMax,
            'scoreMin' => $scoreMin,
            'scoreMax' => $scoreMax
        ];

        // define subset according to fixed Bornes
        if (0 != $fixedBornes->count()) {
            $subset = [];
            $cpt = 0;
            /** @var Border $fixedBorne */
            foreach ($fixedBornes as $fixedBorne) {
                if (empty($subset)) {
                    $subset[$cpt] = [
                        'noteMin' => $noteMin,
                        'noteMax' => $fixedBorne->getNote(),
                        'scoreMin' => $scoreMin,
                        'scoreMax' => $fixedBorne->getScore()
                    ];
                    $cpt++;
                    continue;
                }

                $subset[$cpt] = [
                    'noteMin' => $subset[$cpt - 1]['noteMax'],
                    'noteMax' => $fixedBorne->getNote(),
                    'scoreMin' => $subset[$cpt - 1]['scoreMax'],
                    'scoreMax' => $fixedBorne->getScore()
                ];

                $cpt++;
            }

            $subset[$cpt] = [
                'noteMin' => $subset[$cpt - 1]['noteMax'],
                'noteMax' => $noteMax,
                'scoreMin' => $subset[$cpt - 1]['scoreMax'],
                'scoreMax' => $scoreMax
            ];
        }

        //looking for median index
        $medianIndex = null;
        if (null !== $medianNote) {
            $percentiles = $this->getPercentilesForExamClassification($examClassification, $programChannel, $scoreMin, $scoreMax);
            $filledPercentiles = $this->fillAllCentiles($percentiles);
            $medianPercentil = 0.5;
            $gap = null;

            for ($i = 0; $i < count($filledPercentiles); $i++) {
                if (null === $gap) {
                    $gap = abs($medianPercentil - $filledPercentiles[$i]['centil']);
                    $medianIndex = $i;
                    continue;
                }

                if ($gap > abs($medianPercentil - $filledPercentiles[$i]['centil'])) {
                    $gap = abs($medianPercentil - $filledPercentiles[$i]['centil']);
                    $medianIndex = $i;
                }
            }

            $subset = [[
                'noteMin' => 0.01,
                'noteMax' => $medianNote,
                'scoreMin' => $scoreMin,
                'scoreMax' => (float) $filledPercentiles[$medianIndex]['available_score']
            ]];

            $subset[] = [
                'noteMin' => $medianNote,
                'noteMax' => 20,
                'scoreMin' => (float) $filledPercentiles[$medianIndex]['available_score'],
                'scoreMax' => $scoreMax
            ];
        }

        $notes = [];

        foreach ($subset as $ensemble) {
            $notes = array_merge($notes, $this->setNotes($examClassification, $programChannel, $medianNote, $ensemble['scoreMin'], $ensemble['scoreMax'], $ensemble['noteMin'], $ensemble['noteMax']));
        }

        // apply clipping
        if (null !== $lowClipping || null !== $highClipping) {
            $percentiles = $this->getPercentilesForExamClassification($examClassification, $programChannel, $scoreMin, $scoreMax);
            $filledCentiles = $this->fillAllCentiles($percentiles);

            $countFilledCentiles = count($filledCentiles);

            for ($i = 0; $i < $countFilledCentiles; $i++) {
                if (null !== $lowClipping && $filledCentiles[$i]['centil'] < $lowClipping / 100) {
                    $notes[$i]['note'] = $noteMin;
                }

                if (null !== $highClipping && $filledCentiles[$i]['centil'] > (100 - $highClipping) / 100) {
                    $notes[$i]['note'] = $noteMax;
                }
            }
        }

        return $notes;
    }

    public function getEligibleStudents(array $programChannels, ?float $score = null, ?bool $eligible = true): array
    {
        $students = [];
        /** @var ProgramChannel $programChannel */
        foreach($programChannels as $programChannel) {
            $students[$programChannel->getName()] = $this->studentRepository->getEligibleStudents(programChannel: $programChannel, score: $score, eligible: $eligible);
        }

        return $students;
    }
}
