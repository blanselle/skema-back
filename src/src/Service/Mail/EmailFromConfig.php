<?php

declare(strict_types=1);

namespace App\Service\Mail;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailFromConfig
{
    public function __construct(
        private ParameterBagInterface $params,
    ) {
    }

    public function get(string $key): string
    {
        return strval($this->params->get($key));
    }
}
