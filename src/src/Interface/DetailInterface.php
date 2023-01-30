<?php

declare(strict_types=1);

namespace App\Interface;

/**
 * Représente une entité avec un champs detail
 */
interface DetailInterface
{
    public function setDetail(?string $detail): self;
    public function getDetail(): ?string;
}
