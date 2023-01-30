<?php

declare(strict_types=1);

namespace App\Constants\Bloc;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class BlocConstants implements ConstantsInterface
{
    public const BLOC_NOTIFICATION_IMPORT_SCORES_DONE = 'NOTIFICATION_IMPORT_SCORES_DONE';
    public const BLOC_NOTIFICATION_IMPORT_SCORES_NOTIFICATION_STUDENT = 'NOTIFICATION_IMPORT_SCORES_NOTIFICATION_STUDENT';
    public const BLOC_NOTIFICATION_SUMMONS_GENERATED = 'NOTIFICATION_SUMMONS_GENERATED';
    public const BLOC_NOTIFICATION_RESIGNATION = 'RESIGNATION_NOTIFICATION';
    public const BLOC_NOTIFICATION_EXAM_SESSION_RESIGNATION = 'NOTIFICATION_EXAM_SESSION_RESIGNATION';
    public const BLOC_NOTIFICATION_EXAM_SESSION_DELETE = 'NOTIFICATION_EXAM_SESSION_DELETE';
    public const BLOC_PROGRAM_CHANNEL_SWITCHED = 'PROGRAM_CHANNEL_SWITCHED';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
