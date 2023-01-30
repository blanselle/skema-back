<?php

declare(strict_types=1);

namespace App\Validator;

use App\Constants\CV\NbSchoolReportConstants;
use App\Constants\ProgramChannel\ProgramChannelKeyConstants;
use App\Repository\ProgramChannelRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SchoolReportCountValidator extends ConstraintValidator
{
    public function __construct(private ProgramChannelRepository $programChannelRepository)
    {
    }

    public function validate(mixed $bacSup, Constraint $constraint): void
    {
        if (!($constraint instanceof SchoolReportCount)) {
            throw new UnexpectedTypeException($constraint, SchoolReportCount::class);
        }
        
        $ast1 = $this->programChannelRepository->findOneByKey(ProgramChannelKeyConstants::AST1);

        $nbSchoolReport = count($bacSup->getSchoolReports());
        if (!$bacSup->getSchoolReports()->contains($this->context->getObject())) {
            $nbSchoolReport++;
        }

        $programChannel = $bacSup->getCv()->getStudent()->getProgramChannel();
        
        if (
            $programChannel === $ast1 &&
            $nbSchoolReport > NbSchoolReportConstants::NB_MAX_SCHOOL_REPORT_AST1
        ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        $ast2 = $this->programChannelRepository->findOneByKey(ProgramChannelKeyConstants::AST2);

        if (
            $programChannel === $ast2 &&
            $nbSchoolReport > NbSchoolReportConstants::NB_MAX_SCHOOL_REPORT_AST2
        ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }
}
