<?php

namespace App\Twig;

use App\Repository\OralTest\TestTypeRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TestTypeExtension extends AbstractExtension
{
    public function __construct(private TestTypeRepository $repository) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('testTypeLabel', [$this, 'getTestTypeLabel']),
        ];
    }

    public function getTestTypeLabel(int $id): string
    {
        return (string)$this->repository->find($id);
    }
}