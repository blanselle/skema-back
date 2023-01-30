<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use App\Entity\CV\BacSup;
use App\Repository\CV\BacSupRepository;
use App\Service\Cv\BacSupLevel;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacSupYearValidator extends ConstraintValidator
{
    private const BAC_SUP_LEVEL_MIN = 1;

    public function __construct(private BacSupLevel $bacSupLevel, private BacSupRepository $bacSupRepository) {}

    public function validate(mixed $year, Constraint $constraint): void
    {
        if (!($constraint instanceof BacSupYear)) {
            throw new UnexpectedTypeException($constraint, BacSupYear::class);
        }

        /** @var BacSup $bacSup */
        $bacSup = $this->context->getObject();

        // No control if bac sup is dual
        if (null !== $bacSup->getDualPathBacSup()) {
            return;
        }

        $cv = $bacSup->getCv();

        $bacSups = $this->bacSupRepository->findBy(['cv' => $cv], ['id' => 'asc']);

        $level = (null === $bacSup->getParent())? $this->bacSupLevel->get($bacSup, $bacSups) : $this->bacSupLevel->get($bacSup->getParent(), $bacSups);

        if($level < self::BAC_SUP_LEVEL_MIN) {
            throw new Exception('BacSupYear: Invalid level of bacSup');
        }

        $minYear = $cv->getBac()?->getRewardedYear();

        if(self::BAC_SUP_LEVEL_MIN === $level) {
            if (null !== $minYear) {
                // SB-1210 l’année du baccalauréat est inférieure ou égale à l’année du premier bac_sup.
                if(!($minYear <= $year)) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation()
                    ;
                }
                return;
            }

            // SB-889: si le baccalauréat n’est pas renseigné, alors l’année de diplomation doit être strictement supérieure à l’année de naissance du candidat + 10
            $dateOfBirth = DateTimeImmutable::createFromMutable($cv->getStudent()->getDateOfBirth());
            $minYear = (int) $dateOfBirth->modify('+10 year')->format('Y');
            if (!($year > $minYear)) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation()
                ;
            }

            return;
        }

        // SB-889: S’il s’agit de l’année bac+2 ou supérieure, alors vérifier que l’année de diplomation saisie est strictement supérieure à l’année de diplomation précédente
        if($level > self::BAC_SUP_LEVEL_MIN) {
            $minYear = $bacSups[$level - 2]->getYear();
        }

        if(!($year > $minYear)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
