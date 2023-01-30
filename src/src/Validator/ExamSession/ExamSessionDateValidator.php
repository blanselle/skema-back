<?php

declare(strict_types=1);

namespace App\Validator\ExamSession;

use App\Constants\Exam\ExamSessionTypeCodeConstants;
use App\Entity\ProgramChannel;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Repository\Parameter\ParameterRepository;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExamSessionDateValidator extends ConstraintValidator
{
    private const MANAGMENT_EXCEPTION = ['gmat'];

    public function __construct(
        private ParameterRepository $parameterRepository,
        private Security $security,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof ExamSessionDate)) {
            throw new UnexpectedTypeException($constraint, ExamSessionDate::class);
        }

        /** @var ExamSession $session */
        $session = $this->context->getObject();

        /** @var ProgramChannel $programChannel */
        $programChannel = $this->security->getUser()->getStudent()->getProgramChannel();
        
        if(ExamSessionTypeCodeConstants::ANG === $session->getExamClassification()->getExamSessionType()->getCode()) {

            $dateStart = $this->getDateParameter('dateMiniAnglais', $programChannel);
            $dateEnd = $this->getDateParameter('dateFinUploadEpreuveAnglais', $programChannel);

        } elseif(ExamSessionTypeCodeConstants::MANAGEMENT === $session->getExamClassification()->getExamSessionType()->getCode()) {

            $dateStart = $this->getDateParameter('dateMiniManagement', $programChannel);

            if(in_array($session->getExamClassification(), self::MANAGMENT_EXCEPTION, true)) {
                $dateEnd = $this->getDateParameter('dateFinEpreuveManagement', $programChannel);
            } else {
                $dateEnd = $this->getDateParameter('dateFinUploadEpreuveGmat', $programChannel);
            }

        } else {
            return;
        }

        if($value > $dateEnd or $value < $dateStart) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{dateStart}', $dateStart->format('d/m/Y'))
                ->setParameter('{dateEnd}', $dateEnd->format('d/m/Y'))
                ->addViolation();
        }
    }

    private function getDateParameter(string $keyName, ProgramChannel $programChannel): DateTimeInterface
    {
        $parameter = $this->parameterRepository->findOneParameterByKeyNameAndProgramChannel($keyName, $programChannel);

        if(null === $parameter) {
            throw new ParameterNotFoundException($keyName);
        }

        $date = $parameter->getValue();

        if(!$date instanceof DateTime) {
            throw new Exception(sprintf('parameter %s format error', $keyName));
        }

        return $parameter->getValue();
    }
}
