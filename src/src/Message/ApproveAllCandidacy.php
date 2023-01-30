<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\Uuid;

class ApproveAllCandidacy
{
    public function __construct(private Uuid $userId){}

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}