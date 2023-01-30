<?php

namespace App\Form\DataTransformer;

use App\Entity\OralTest\TestType;
use App\Repository\OralTest\TestTypeRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TestTypeToNumberTransformer implements DataTransformerInterface
{
    public function __construct(private TestTypeRepository $repository) {}

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getId();
    }

    public function reverseTransform($value): ?TestType
    {
        if (empty($value)) {
            return null;
        }

        $object = $this->repository->find($value);

        if (null === $object) {
            throw new TransformationFailedException(sprintf(
                'A Test type with number "%s" does not exist!',
                $value
            ));
        }

        return $object;
    }
}