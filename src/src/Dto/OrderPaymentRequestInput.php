<?php

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Constants\Payment\OrderTypeConstants;
use App\Entity\Exam\ExamSession;

final class OrderPaymentRequestInput
{
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => 'Type of order',
            'enum' => [OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION, OrderTypeConstants::SCHOOL_REGISTRATION_FEES],
            'required' => true,
        ]
    )]
    public string $type = '';

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => 'The url to redirect user on FO',
            'required' => true,
        ]
    )]
    public string $redirectUrl = '';

    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "The iri of Exam session (required in case of type registration_fee_for_exam_session)",
            'required' => false,
        ]
    )]
    public ?ExamSession $examSession = null;
}