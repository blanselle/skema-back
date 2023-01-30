<?php

namespace App\Form\DataTransformer;

use App\Entity\OralTest\SlotType;
use App\Repository\OralTest\SlotTypeRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SlotTypeToNumberTransformer implements DataTransformerInterface
{
    public function __construct(private SlotTypeRepository $repository) {}

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getId();
    }

    public function reverseTransform($value): ?SlotType
    {
        if (empty($value)) {
            return null;
        }

        $object = $this->repository->find($value);

        if (null === $object) {
            throw new TransformationFailedException(sprintf(
                'A slot type with number "%s" does not exist!',
                $value
            ));
        }

        return $object;
    }
}