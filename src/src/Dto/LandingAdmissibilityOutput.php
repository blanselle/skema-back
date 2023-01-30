<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;

final class LandingAdmissibilityOutput
{
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "The student fullname",
        ]
    )]
    public string $fullname;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "The student identifier",
        ]
    )]
    public string $identifier;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "The student state",
        ]
    )]
    public string $result;

    #[ApiProperty(
        openapiContext: [
            'type' => 'boolean',
            'description' => "True if student is admissible false else",
        ]
    )]
    public bool $admissible;
}
