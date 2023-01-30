<?php

declare(strict_types=1);

namespace App\Helper;

use DateTimeInterface;
use IntlDateFormatter;

class DateFormatterHelper
{
    public static function formatFull(DateTimeInterface $date): string
    {
        $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
        $fmt->setPattern('EEEE dd MMMM YYYY');

        return ucfirst(strval($fmt->format($date)));
    }
}