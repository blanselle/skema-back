<?php

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;

final class AdmissibilityPublicationOutput
{
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'description' => "The content to display",
        ]
    )]
    public string $content = "<p>Vos résultats d'admissibilté n'ont pas été trouvés</p>";
}