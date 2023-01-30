<?php

declare(strict_types=1);

namespace App\Service\Admissibility;

use App\Constants\Admissibility\Ranking\CoefficientTypeConstants;
use App\Constants\Exam\ExamSessionTypeNameConstants;
use App\Entity\Exam\ExamStudent;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Exception\Admissibility\AdmissibilityNotFoundException;
use App\Exception\Admissibility\CoefficientNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class RankingManager
{
    private const MAX_NOTE = 20;
    private const SCORE = 'score';
    private const COEFFICIENT = 'coefficient';

    private array $coefficients = [];

    public function __construct(private EntityManagerInterface $em) {}

    public function execute(array $programChannels, array $coefficients): array
    {
        try {
            $ranking = [];
            $this->coefficients = $coefficients;
            $this->checkCoefficients();

            if (empty($this->coefficients)) {
                throw new CoefficientNotFoundException(blocKey: implode(', ', array_map(fn(ProgramChannel $programChannel) => $programChannel->getName(), $programChannels)));
            }

            /** @var ProgramChannel $programChannel */
            foreach ($programChannels as $programChannel) {
                $students = $this->em->getRepository(Student::class)->getValidStudentshipRanking($programChannel);

                /** @var  Student $student */
                foreach ($students as $student)
                {
                    $cvNote = $this->getCVNoteWithCoeff(note: $student->getGlobalCvNote(), programChannel: $programChannel);
                    $englishNote = $this->getBestExamNoteWithCoeffByType(programChannel: $programChannel, type: ExamSessionTypeNameConstants::TYPE_ENGLISH, examStudent: $student->getEnglishNoteUsed());
                    $managementNote = $this->getBestExamNoteWithCoeffByType(programChannel: $programChannel, type: ExamSessionTypeNameConstants::TYPE_MANAGEMENT, examStudent: $student->getManagementNoteUsed());
                    $totalScore = round($cvNote[self::SCORE] + $englishNote[self::SCORE] + $managementNote[self::SCORE], 2);
                    $totalCoefficients = $cvNote[self::COEFFICIENT] + $englishNote[self::COEFFICIENT] + $managementNote[self::COEFFICIENT];
                    $student->setAdmissibilityGlobalScore($totalScore);
                    $student->setAdmissibilityGlobalNote($totalScore / $totalCoefficients);
                    $student->setAdmissibilityMaxScore($totalCoefficients * 20);
                }
                
                $ranking[$programChannel->getPositionKey()] = [
                    'students' => $this->applyRanking($students),
                    'programChannel' => $programChannel,
                ];
                
                $this->em->flush();
            }
        } catch (AdmissibilityNotFoundException|CoefficientNotFoundException $e) {
            throw $e;
        }

        return $ranking;
    }

    private function checkCoefficients(): void
    {
        foreach ($this->coefficients as $key => $coefficients)
        {
            if (count($coefficients) != count(CoefficientTypeConstants::getConsts())) {
                throw new CoefficientNotFoundException($key);
            }
        }
    }

    public function getCVNoteWithCoeff(float $note, ProgramChannel $programChannel): array
    {
        if ($note > self::MAX_NOTE) {
            $note = self::MAX_NOTE;
        }

        return [
            self::SCORE => $note * $this->coefficients[$programChannel->getPositionKey()][CoefficientTypeConstants::TYPE_CV]['value'],
            self::COEFFICIENT => $this->coefficients[$programChannel->getPositionKey()][CoefficientTypeConstants::TYPE_CV]['value']
        ];
    }

    private function getBestExamNoteWithCoeffByType(ProgramChannel $programChannel, string $type, ?ExamStudent $examStudent): array
    {
        return [
            self::SCORE => (float)$examStudent?->getAdmissibilityNote() * $this->coefficients[$programChannel->getPositionKey()][$type]['value'],
            self::COEFFICIENT => $this->coefficients[$programChannel->getPositionKey()][$type]['value']
        ];
    }

    private function applyRanking(array $students): array
    {
        usort($students, function($a, $b) {
            if ($a->getAdmissibilityGlobalScore() == $b->getAdmissibilityGlobalScore()) {
                return 0;
            }
            return ($a->getAdmissibilityGlobalScore() > $b->getAdmissibilityGlobalScore()) ? -1 : 1;
        });

        $i = 1;
        $index = 1;
        $latestScore = null;
        /** @var Student $student */
        foreach ($students as $key => $student) {
            if ($latestScore != $student->getAdmissibilityGlobalScore()) {
                $index = $i;
            }
            $student->setAdmissibilityRanking($index);
            $latestScore = $student->getAdmissibilityGlobalScore();
            $i++;
        }

        usort($students, function($a, $b) {
            if ($a->getAdmissibilityRanking() == $b->getAdmissibilityRanking()) {
                return 0;
            }
            return ($a->getAdmissibilityRanking() < $b->getAdmissibilityRanking()) ? -1 : 1;
        });

        return $students;
    }
}