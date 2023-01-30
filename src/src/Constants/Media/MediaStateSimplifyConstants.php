<?php

namespace App\Constants\Media;

class MediaStateSimplifyConstants
{
    public const MISSING = 'missing';
    public const TO_VALIDATE = 'toValidate';

    public const MEDIA_STATES = [
        MediaStateSimplifyConstants::MISSING => 'Documents manquant',
        MediaStateSimplifyConstants::TO_VALIDATE => 'Documents à contrôler',
    ];
}