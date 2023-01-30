<?php

declare(strict_types=1);

namespace App\Exception\Bloc;

use Exception;

class BlocNotFoundException extends Exception
{
    public function __construct(string $blocKey = '')
    {
        parent::__construct(
            message: sprintf('bloc %s is missing', $blocKey),
            code: 500,
        );
    }
}
