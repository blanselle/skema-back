<?php

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SchoolReportMediaMissingValidator extends ConstraintValidator
{
    public function validate(mixed $media, Constraint $constraint): void
    {
        if (!($constraint instanceof SchoolReportMediaMissing)) {
            throw new UnexpectedTypeException($constraint, SchoolReportMediaMissing::class);
        }

        $schoolReport = $this->context->getObject();
        if(null !== $schoolReport->getScore() and null !== $media) {
            return;
        }

        if(true === $schoolReport->isScoreNotOutOfTwenty() and null !== $media) {
            return;
        }

        // Le numéro du bacSup c'est sa position dans le tableau des bacSup du Cv +1
        $numBacSup = $schoolReport->getBacSup()->getCv()->getBacSups()->indexOf($schoolReport->getBacSup()) + 1;

        if($numBacSup === 1) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;

            return;
        }

        if($numBacSup === 2 && 'ast2' === $schoolReport->getBacSup()->getCv()->getStudent()->getProgramChannel()->getKey()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;

            return;
        }
    }
}