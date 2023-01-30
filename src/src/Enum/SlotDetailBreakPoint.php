<?php

namespace App\Enum;

enum SlotDetailBreakPoint: int
{
    case NONE_PRIORITY = 0;
    case LAST_SLOT_BEFORE_MORNING_BREAK = 1;
    case LAST_SLOT_ON_MORNING = 2;
    case LAST_SLOT_BEFORE_AFTERNOON_BREAK = 3;
    case LAST_SLOT_ON_AFTERNOON = 4;
    case LAST_SLOT_BEFORE_EVENING_BREAK = 5;
    case LAST_SLOT_ON_EVENING = 6;


    private static function checkMorning(SlotDetailBreakPoint $value): bool
    {
        return match($value) {
            SlotDetailBreakPoint::LAST_SLOT_BEFORE_MORNING_BREAK, SlotDetailBreakPoint::LAST_SLOT_ON_MORNING => true,
            default => false,
        };
    }

    private static function checkAfernoon(SlotDetailBreakPoint $value): bool
    {
        return match($value) {
            SlotDetailBreakPoint::LAST_SLOT_BEFORE_AFTERNOON_BREAK, SlotDetailBreakPoint::LAST_SLOT_ON_AFTERNOON => true,
            default => false,
        };
    }

    private static function checkEvening(SlotDetailBreakPoint $value): bool
    {
        return match($value) {
            SlotDetailBreakPoint::LAST_SLOT_BEFORE_EVENING_BREAK, SlotDetailBreakPoint::LAST_SLOT_ON_EVENING => true,
            default => false,
        };
    }

    public function isMorning(): bool
    {
        return static::checkMorning($this);
    }

    public function isAfertnoon(): bool
    {
        return static::checkAfernoon($this);
    }

    public function isEvening(): bool
    {
        return static::checkEvening($this);
    }
}
