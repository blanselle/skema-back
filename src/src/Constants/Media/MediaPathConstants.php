<?php

declare(strict_types=1);

namespace App\Constants\Media;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class MediaPathConstants implements ConstantsInterface
{
    public const ROOT_PATH      = 'kernel.project_dir';
    public const PRIVATE_PATH   = 'medias_private_path';
    public const FIXTURE_PATH   = 'medias_fixture_path';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
