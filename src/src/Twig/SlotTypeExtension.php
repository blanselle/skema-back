<?php

namespace App\Twig;

use App\Repository\OralTest\SlotTypeRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SlotTypeExtension extends AbstractExtension
{
    public function __construct(private SlotTypeRepository $repository) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('slotTypeLabel', [$this, 'getSlotTypeLabel']),
        ];
    }

    public function getSlotTypeLabel(int $id): string
    {
        return (string)$this->repository->find($id);
    }
}