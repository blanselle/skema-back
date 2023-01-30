<?php

declare(strict_types=1);

namespace App\Exception\Ruler;

use Exception;

class BonusNotFoundException extends Exception
{
    public function __construct(string $bonus = '')
    {
        parent::__construct(
            message: sprintf('bonus %s is missing', $bonus),
            code: 500,
        );
    }
}
