<?php

declare(strict_types=1);

namespace App\Helper;

class TokenHelper
{    
    public static function createToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');
    }
}