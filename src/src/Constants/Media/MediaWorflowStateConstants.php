<?php

declare(strict_types=1);

namespace App\Constants\Media;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class MediaWorflowStateConstants implements ConstantsInterface
{
    public const STATE_UPLOADED = 'uploaded';
    public const STATE_TO_CHECK = 'to_check';
    public const STATE_TRANSFERED = 'transfered';
    public const STATE_ACCEPTED = 'accepted';
    public const STATE_REJECTED = 'rejected';
    public const STATE_CANCELLED = 'cancelled';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
