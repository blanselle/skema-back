<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;

final class CandidacyOutput
{
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "Administrative record completion status",
        ]
    )]
    public string $administrativeRecord;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "Competition fees payment record completion status",
        ]
    )]
    public string $competitionFeesPayment;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "Cv record completion status",
        ]
    )]
    public string $cv;

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "Written examination record completion status",
        ]
    )]
    public string $writtenExamination;

    #[ApiProperty(
        openapiContext: [
            'type' => 'boolean',
            'description' => "Return true if has a scholar ship media",
        ]
    )]
    public bool $hasScholarShipMedia = false;

    #[ApiProperty(
        openapiContext: [
            'type' => 'boolean',
            'description' => "Return true if has a scholar report media",
        ]
    )]
    public bool $hasScholarReportMedia = false;

    #[ApiProperty(
        openapiContext: [
            'type' => 'boolean',
            'description' => "Return true if has a score on each exam student",
        ]
    )]
    public bool $hasScore = false;
}