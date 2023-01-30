<?php

declare(strict_types=1);

namespace App\Constants\Media;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class MediaWorkflowTransitionConstants implements ConstantsInterface
{
    public const UPLOADED_TO_CHECK = 'uploaded_to_check';
    public const CHECK_TO_ACCEPTED = 'check_to_accepted';
    public const CHECK_TO_REJECTED = 'check_to_rejected';
    public const ACCEPTED_TO_REJECTED = 'accepted_to_rejected';
    public const ACCEPTED_TO_CHECK = 'accepted_to_check';
    public const CHECK_TO_TRANSFERED = 'check_to_transfered';
    public const TRANSFERED_TO_ACCEPTED = 'transfered_to_accepted';
    public const TRANSFERED_TO_REJECTED = 'transfered_to_rejected';
    public const TO_CANCEL = 'to_cancel';


    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
