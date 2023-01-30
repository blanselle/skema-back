<?php

declare(strict_types=1);

namespace App\Service\Cv;

use App\Constants\Bac\BacTypeConstants;
use App\Constants\CV\TagBacConstants;

class GetTypeBacFromYear
{
    public function get(int $year): string
    {
        if ($year >= BacTypeConstants::BAC_TYPES_MODIFICATIONS_YEAR) {
            return TagBacConstants::getConsts()['V2'];
        }

        return TagBacConstants::getConsts()['V1'];
    }
}
