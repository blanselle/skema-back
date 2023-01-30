<?php

declare(strict_types=1);

namespace App\Message;

class CvCalculationMessage
{
    public function __construct(private int $cvId)
    {
    }

    public function getCvId(): int
    {
        return $this->cvId;
    }

    public function setCvId(int $cvId): self
    {
        $this->cvId = $cvId;
        
        return $this; 
    }
}
