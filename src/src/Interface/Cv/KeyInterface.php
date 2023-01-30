<?php

declare(strict_types=1);

namespace App\Interface\Cv;

/**
 * Protège de la suppression les entités qui possèdent des key
 */
interface KeyInterface
{
    public function getKey(): ?string;

    public function setKey(?string $key): self;
}
