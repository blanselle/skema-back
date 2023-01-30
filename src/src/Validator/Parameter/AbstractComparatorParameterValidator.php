<?php

declare(strict_types=1);

namespace App\Validator\Parameter;

use App\Entity\Parameter\Parameter;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Repository\Parameter\ParameterRepository;
use App\Repository\ProgramChannelRepository;
use DateTimeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

abstract class AbstractComparatorParameterValidator extends ConstraintValidator
{
    public function __construct(private ParameterRepository $parameterRepository, private ProgramChannelRepository $programChannelRepository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof AbstractComparatorParameter)) {
            throw new UnexpectedTypeException($constraint, AbstractComparatorParameter::class);
        }
        
        $programChannel = $this->programChannelRepository->find((int)$constraint->programChannelId);

        if (null !== $programChannel) {
            $parameter = $this->parameterRepository->findOneParameterByKeyNameAndProgramChannel(key: $constraint->parameterName, programChannel: $programChannel);
        } else {
            $parameter = $this->parameterRepository->findOneParameterByKeyName($constraint->parameterName);
        }

        if(null === $parameter) {
            throw new ParameterNotFoundException($constraint->parameterName);
        }

        if(false === $this->comparaison($value, $parameter)) {
            $this->buildViolation($parameter, $constraint);
        }
    }

    abstract protected function comparaison(DateTimeInterface|int|float $value, Parameter $parameter): bool;

    protected function buildViolation(Parameter $parameter, Constraint $constraint): void
    {
        $value = $parameter->getValue();
        if($value instanceof DateTimeInterface) {
            $value = $parameter->getValue()->format('d/m/Y');
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ property }}', $this->context->getPropertyName())
            ->setParameter('{{ parameter }}', strval($value))
            ->addViolation()
        ;
    }
}
