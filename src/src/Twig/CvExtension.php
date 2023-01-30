<?php

namespace App\Twig;

use App\Repository\CV\BacSupRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CvExtension extends AbstractExtension
{
    public function __construct(private BacSupRepository $bacSupRepository) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('canAddBacSup', [$this, 'canAddBacSup']),
            new TwigFilter('canSchoolReport', [$this, 'canSchoolReport']),
        ];
    }

    public function canAddBacSup(int $cvId): bool
    {
        return count($this->bacSupRepository->getMainsBacSup(cvId: $cvId)) < 5;
    }

    public function canSchoolReport(int $bacSupId): bool
    {
        return true;
    }
}