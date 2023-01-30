<?php

declare(strict_types=1);

namespace App\Service\Dashboard;

use App\Constants\Dashboard\DashboardMediaLabelConstants;
use App\Model\Dashboard\Row;
use App\Repository\MediaRepository;

class DashboardMediaMissingCounter
{
    public function __construct(private MediaRepository $mediaRepository) {}

    public function getRows(array $programChannels): array
    {
        $rows = []; // Liste des lignes du tableau

        foreach ($programChannels as $programChannel) {
            $mediaByCode = $this->mediaRepository->findNbStudentWithMediaMissing($programChannel);

            foreach ($mediaByCode as $item) {
                $code = $item['code'];
                if (!isset($rows[$code])) {
                    $rows[$code] = (new Row())
                        ->setLabel(constant(DashboardMediaLabelConstants::class . '::'.$code))
                        ->setKey(strtolower($item['code']))
                    ;
                }
                $rows[$code]->addValue($item['count']);
            }
        }

        return $rows;
    }
}
