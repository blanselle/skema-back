<?php

namespace App\Twig;

use App\Entity\Loggable\History;
use App\Service\Loggable\HistoryDescription;
use Gedmo\Loggable\Entity\LogEntry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LoggableExtension extends AbstractExtension
{
    public function __construct(private HistoryDescription $historyDescription) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('logDescription', [$this, 'getDescription']),
        ];
    }

    public function getDescription(History|LogEntry $log, string $key, mixed $value): string
    {
        return $this->historyDescription->getDescription(log: $log, key: $key, value: $value);
    }
}