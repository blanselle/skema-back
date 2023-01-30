<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use App\Entity\CV\Bac\Bac;
use App\Repository\CV\BacSupRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacYearValidator extends ConstraintValidator
{
    public function __construct(private BacSupRepository $bacSupRepository) {}

    public function validate(mixed $year, Constraint $constraint): void
    {
        if (!($constraint instanceof BacYear)) {
            throw new UnexpectedTypeException($constraint, BacYear::class);
        }

        /** @var Bac $bacSup */
        $bac = $this->context->getObject();

        $cv = $bac->getCv();

        $birthDate = $cv->getStudent()->getDateOfBirth()->format('Y');

        if($year <= intval($birthDate)) {
            $this->context->buildViolation($constraint->dateOfBirthMessage)
                ->setParameter('{{ expected }}', $birthDate)
                ->addViolation()
            ;
        }

        $firstBacSup = $this->bacSupRepository->findOneBy(['cv' => $cv]);

        if(null === $firstBacSup) {
            return;
        }

        // SB-1210 l’année du baccalauréat est inférieure ou égale à l’année du premier bac_sup.
        if(!($year <= $firstBacSup->getYear())) {
            $this->context->buildViolation($constraint->firstBacSupMessage)
                ->setParameter('{{ expected }}', strval($firstBacSup->getYear()))
                ->addViolation()
            ;
        }
    }
}
