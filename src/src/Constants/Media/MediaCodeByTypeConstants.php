<?php

declare(strict_types=1);

namespace App\Constants\Media;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class MediaCodeByTypeConstants implements ConstantsInterface
{
    public const BULLETIN_SEMESTRIAL = [
        MediaCodeConstants::CODE_BULLETIN_L1_S1,
        MediaCodeConstants::CODE_BULLETIN_L1_S2,
        MediaCodeConstants::CODE_BULLETIN_L2_S3,
        MediaCodeConstants::CODE_BULLETIN_L2_S4,
        MediaCodeConstants::CODE_BULLETIN_L3_S5,
        MediaCodeConstants::CODE_BULLETIN_L3_S6,
        MediaCodeConstants::CODE_BULLETIN_M1_S1,
        MediaCodeConstants::CODE_BULLETIN_M1_S2,
        MediaCodeConstants::CODE_BULLETIN_M2_S3,
        MediaCodeConstants::CODE_BULLETIN_M2_S4,
    ];
    public const BULLETIN_ANNUAL = [
        MediaCodeConstants::CODE_BULLETIN_L1,
        MediaCodeConstants::CODE_BULLETIN_L2,
        MediaCodeConstants::CODE_BULLETIN_L3,
        MediaCodeConstants::CODE_BULLETIN_M1,
        MediaCodeConstants::CODE_BULLETIN_M2,
    ];

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
