<?php

namespace App\Helper;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class DateHelper
{
    public static function getWorkingDays(DateTimeInterface $start, DateTimeInterface $end, ?array $publicHolidays = []): array
    {
        $dates = [];
        $current = new DateTime($start->format('Y-m-d'));
        do {
            // Remove public holiday and sunday
            if (!(in_array($current->format('Y-m-d'), $publicHolidays, true) or (int)$current->format('N') === 7)) {
                $dates[] = new DateTimeImmutable($current->format('Y-m-d'));
            }

            $current->add(new DateInterval('P1D'));
        } while($current <= $end);

        return $dates;
    }

    /**
     * Return the date available from the next day (of the current date) plus the number of days (limit)
     * excluding Sundays and public holidays.
     */
    public static function getWorkDay(DateTimeInterface $date, int $limit, ?array $publicHolidays = []): DateTimeImmutable
    {
        $start = 0;
        $current = new DateTime($date->format('Y-m-d'));
        do {
            $current->add(new DateInterval('P1D'));

            // Remove public holiday and sunday
            if (!(in_array($current->format('Y-m-d'), $publicHolidays, true) or (int)$current->format('N') === 7)) {
                $start++;
            }
        } while($start <= $limit);

        return new DateTimeImmutable($current->format('Y-m-d'));
    }
}