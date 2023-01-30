<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;

final class AdmissibilityResultOutput
{
    #[ApiProperty(
        openapiContext: [
            'type' => 'integer',
            'description' => "Student admissibility score",
        ]
    )]
    public ?float $score;

    #[ApiProperty(
        openapiContext: [
            'type' => 'integer',
            'description' => "Student admissibility maximum score",
        ]
    )]
    public ?float $scoreMax;

    #[ApiProperty(
        openapiContext: [
            'type' => 'boolean',
            'description' => "If the student is admissible",
        ]
    )]
    public bool $admissible;
}