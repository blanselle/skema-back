<?php

declare(strict_types=1);

namespace App\DataFixtures\Providers;

use DateTime;

class StringProvider
{
    public function escape(string $string): string
    {
        return $string;
    }

    public function displayCurrentDate(string $format): string
    {
        return (new  DateTime())->format($format);
    }

    public function replace(string $search, string $replace, string $string): string
    {
        return str_replace($search, $replace, $string);
    }
}
