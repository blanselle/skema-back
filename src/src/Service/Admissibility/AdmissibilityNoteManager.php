<?php

namespace App\Service\Admissibility;

use App\Constants\Exam\ExamSessionTypeNameConstants;
use App\Exception\Admissibility\AdmissibilityNotFoundException;
use App\Repository\Admissibility\AdmissibilityRepository;
use App\Repository\Admissibility\ConversionTableRepository;
use App\Repository\Admissibility\ParamRepository;
use App\Repository\Exam\ExamStudentRepository;
use App\Repository\StudentRepository;

class AdmissibilityNoteManager
{
    public function __construct(
        private AdmissibilityRepository $admissibilityRepository,
        private ConversionTableRepository $conversionTableRepository,
        private ExamStudentRepository $examStudentRepository,
        private StudentRepository $studentRepository,
        private ParamRepository $paramRepository
    ) {}

    public function update(array $student): void
    {
        $examStudents = $this->examStudentRepository->fetchExamStudentsForHandler($student['id']);
        foreach ($examStudents as $examStudent) {
            $admissibility = $this->admissibilityRepository->findOneBy(['examClassification' => $examStudent['exam_classification_id']]);
            if (null == $admissibility) {
                throw new AdmissibilityNotFoundException(message: sprintf('Admissibility not found for %s and student %d', $examStudent['exam_classification_name'], $student['student_identifier']));
            }
            $param = $this->paramRepository->findOneBy(['programChannel' => $student['program_channel_id'], 'admissibility' => $admissibility->getId()]);
            if (null == $param) {
                throw new AdmissibilityNotFoundException(message: sprintf('Parameter not found for %s and student %d', $examStudent['exam_classification_name'], $student['student_identifier']));
            }
            $conversion = $this->conversionTableRepository->findOneBy(['param' => $param, 'score' => (float)$examStudent['score']]);
            if (null === $conversion) {
                throw new AdmissibilityNotFoundException(message: sprintf('Conversion table not found for %s and student %d and score %d', $examStudent['exam_classification_name'], $student['student_identifier'], (float)$examStudent['score']));
            }

            $this->examStudentRepository->updateAdmissibilityNote($examStudent['id'], $conversion->getNote());
        }
        $englishNoteUsed = $this->examStudentRepository->getBestAdmissibilityNoteByType(id: $student['id'], type: ExamSessionTypeNameConstants::TYPE_ENGLISH);
        if (null !== $englishNoteUsed) {
            $this->studentRepository->updateNoteUsed(studentId: $student['id'], type: ExamSessionTypeNameConstants::TYPE_ENGLISH, examStudentId: $englishNoteUsed->getId());
        }
        $managementNoteUsed = $this->examStudentRepository->getBestAdmissibilityNoteByType(id: $student['id'], type: ExamSessionTypeNameConstants::TYPE_MANAGEMENT);
        if (null !== $managementNoteUsed) {
            $this->studentRepository->updateNoteUsed(studentId: $student['id'], type: ExamSessionTypeNameConstants::TYPE_MANAGEMENT, examStudentId: $managementNoteUsed->getId());
        }
    }
}