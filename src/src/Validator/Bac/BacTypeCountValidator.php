<?php

declare(strict_types=1);

namespace App\Validator\Bac;

use App\Constants\CV\BacChannelConstants;
use App\Constants\CV\TagBacConstants;
use App\Service\Cv\GetTypeBacFromYear;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacTypeCountValidator extends ConstraintValidator
{
    public function __construct(private GetTypeBacFromYear $getTypebacFromYear) {}
    
    public function validate(mixed $object, Constraint $constraint): void
    {
        if (!($constraint instanceof BacTypeCount)) {
            throw new UnexpectedTypeException($constraint, BacTypeCount::class);
        }
        
        $bac = $this->context->getObject();

        $bacChannel = $bac->getBacChannel();

        if($bacChannel === null) {
            return;
        }
        
        $nbBacTypes = count($bac->getBacTypes());
        
        switch($bac->getBacChannel()->getKey()) {

            case BacChannelConstants::PROFESSIONAL: 

                if($nbBacTypes !== 0) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation()
                    ;
                }

            return;

            case BacChannelConstants::GENERAL: 

                if(null === $bac->getRewardedYear()) {
                    return;
                }

                if($nbBacTypes !== 1 && $this->getTypebacFromYear->get($bac->getRewardedYear()) === TagBacConstants::V1) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation()
                    ;
                    return;
                }
    
                if($nbBacTypes !== 2 && $this->getTypebacFromYear->get($bac->getRewardedYear()) === TagBacConstants::V2) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation()
                    ;
                    return;
                }

            return;


            case BacChannelConstants::TECHNOLOGIE: 

                if($nbBacTypes !== 1) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation()
                    ;
                }

            return;
        }
    }
}
