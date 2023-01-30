<?php

namespace App\Form\DataTransformer;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CampusToNumberTransformer implements DataTransformerInterface
{
    public function __construct(private CampusRepository $campusRepository) {}

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getId();
    }

    public function reverseTransform($value): ?Campus
    {
        if (empty($value)) {
            return null;
        }

        $campus = $this->campusRepository->find($value);

        if (null === $campus) {
            throw new TransformationFailedException(sprintf(
                'A campus with number "%s" does not exist!',
                $value
            ));
        }

        return $campus;
    }
}