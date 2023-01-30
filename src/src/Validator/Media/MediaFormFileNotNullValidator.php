<?php

declare(strict_types=1);

namespace App\Validator\Media;

use App\Entity\Media;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MediaFormFileNotNullValidator extends ConstraintValidator
{
    public function validate(mixed $object, Constraint $constraint): void
    {
        if (!($constraint instanceof MediaFormFileNotNull)) {
            throw new UnexpectedTypeException($constraint, MediaFormFileNotNull::class);
        }

        /** @var Media $media */
        $media = $this->context->getObject();
        
        if($media->getFile() === null && $object === null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
