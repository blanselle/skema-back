<?php

namespace App\Helper;

use App\Entity\OralTest\SlotConfiguration;
use App\Entity\OralTest\TestConfiguration;
use App\Enum\SlotDetailBreakPoint;
use DateInterval;
use Exception;

class SlotDetailHelper
{
    /**
     * Calcul
     * Créneau 1 =
     * Début : {heure de début session}
     * Fin : {début créneau} + {durée prépa} + {durée épreuve}
     * Créneau 2 =
     * Début : {fin créneau 1} + {durée entre 2 jurys} [- {durée épreuve} si {durée prépa}> 0]
     * Fin : {début créneau} + {durée prépa} + {durée épreuve}
     *
     * @return array an array of SlotDetail
     */
    public static function compute(TestConfiguration $testConfiguration, ?int $juryDebriefDuration = 0): array
    {
        $slots = [];

        $juryDebriefDurationInterval = new DateInterval("PT0H{$juryDebriefDuration}M");

        $preparationTime = $testConfiguration->getPreparationTime() ?? 0;
        $preparationTimeInterval = new DateInterval("PT0H{$preparationTime}M");
        $durationOfTest = $testConfiguration->getDurationOfTest() ?? 0;
        $durationOfTestInterval = new DateInterval("PT0H{$durationOfTest}M");

        // sort by start_time
        $slotConfigurations = $testConfiguration->getSlotConfigurations()->toArray();
        usort($slotConfigurations, fn(SlotConfiguration $a, SlotConfiguration $b) => $a->getStartTime() > $b->getStartTime() ? 1 : -1);

        foreach ($slotConfigurations as $slotConfiguration) {
            $slotType = $slotConfiguration->getSlotType()?->getCode();
            if (null === $slotType) {
                throw new Exception("Slot type for slot configuration {$slotConfiguration->getId()} not found");
            }
            $startTime = $slotConfiguration->getStartTime();
            $endTime = $slotConfiguration->getEndTime();
            $breakTime = $slotConfiguration->getBreakTime();
            $breakDuration = $slotConfiguration->getBreakDuration() ?? 0;
            $breakDurationInterval = new DateInterval("PT0H{$breakDuration}M");
            $start = $startTime;
            $firstSlot = true;

            do {
                $slotDetail = ['break_point' => SlotDetailBreakPoint::NONE_PRIORITY];

                if (!$firstSlot) {
                    // From the second slot Start : {end of previous slot} + {jury debrief duration interval}
                    $start = $start->add($juryDebriefDurationInterval);

                    // Add duration of test if preparation time is not equal to 0
                    if (0 !== $preparationTime) {
                        $start = $start->add($durationOfTestInterval);
                    }
                } else {
                    $firstSlot = false;
                }

                // End of slot {start} + {preparation time} + {duration of test}
                $end = $start->add($durationOfTestInterval);
                if (0 !== $preparationTime) {
                    $end = $end->add($preparationTimeInterval);
                }

                $slotDetail['start_time'] = $start;
                $slotDetail['end_time'] = $end;

                $start = $end;

                /**
                 * Calculate the break point
                 * 0: none priority
                 * 1: last slot before morning break
                 * 2: last slot on morning
                 * 3: last slot before afternoon break
                 * 4: last slot on afternoon
                 * 5: last slot before evening break
                 * 6: last slot on evening
                 */
                if ($end <= $breakTime and $end->add($durationOfTestInterval) >= $breakTime) {
                    $slotDetail['break_point'] = match ($slotType) {
                        'M' => SlotDetailBreakPoint::LAST_SLOT_BEFORE_MORNING_BREAK,
                        'A' => SlotDetailBreakPoint::LAST_SLOT_BEFORE_AFTERNOON_BREAK,
                        'S' => SlotDetailBreakPoint::LAST_SLOT_BEFORE_EVENING_BREAK,
                        default => SlotDetailBreakPoint::NONE_PRIORITY
                    };

                    $start = $end->add($breakDurationInterval);
                }
                if ($end->add($juryDebriefDurationInterval) >= $endTime) {
                    $slotDetail['break_point'] = match ($slotType) {
                        'M' => SlotDetailBreakPoint::LAST_SLOT_ON_MORNING,
                        'A' => SlotDetailBreakPoint::LAST_SLOT_ON_AFTERNOON,
                        'S' => SlotDetailBreakPoint::LAST_SLOT_ON_EVENING,
                        default => SlotDetailBreakPoint::NONE_PRIORITY
                    };
                }

                $slots[] = $slotDetail;
            } while ($start->add($juryDebriefDurationInterval) < $endTime);

        }

        return $slots;
    }
}