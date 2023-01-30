<?php

declare(strict_types=1);

namespace App\Service\Dashboard;

use App\Constants\Dashboard\DashboardMediaLabelConstants;
use App\Model\Dashboard\Row;
use App\Repository\MediaRepository;

class DashboardMediaToValidateCounter
{
    public function __construct(private MediaRepository $mediaRepository) {}

    public function getRows(array $programChannels): array
    {
        $rows = []; // Liste des lignes du tableau

        foreach ($programChannels as $programChannel) {
            $mediaByCode = $this->mediaRepository->findNbStudentWithMediaToValidate($programChannel);
            foreach ($mediaByCode as $item) {
                $code = $item['code'];
                if (!isset($rows[$code])) {
                    $rows[$code] = (new Row())
                        ->setLabel(constant(DashboardMediaLabelConstants::class . '::'.$code))
                        ->setKey(strtolower($code))
                    ;
                }
                $rows[$code]->addValue($item['count']);
            }
        }

        return $rows;
    }
}
