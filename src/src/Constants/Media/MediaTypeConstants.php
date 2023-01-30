<?php

declare(strict_types=1);

namespace App\Constants\Media;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class MediaTypeConstants implements ConstantsInterface
{
    public const TYPE_IMAGE_CMS = 'image_cms';
    public const TYPE_DOCUMENT_TO_VALIDATE = 'document_to_validate';
    public const TYPE_DOCUMENT_SIMPLE = 'document_simple';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
