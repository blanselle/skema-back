<?php

declare(strict_types=1);

namespace App\Service\Cv;

use App\Constants\CV\BacSupConstants;
use App\Constants\Media\MediaCodeByTypeConstants;
use App\Entity\CV\SchoolReport;
use App\Repository\CV\BacSupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class BacSupSchoolReportCode
{
    public function __construct(private BacSupRepository $bacSupRepository) {}

    public function get(SchoolReport $schoolReport): string
    {
        if($schoolReport->getBacSup()->getType() === BacSupConstants::TYPE_ANNUAL) {
            $list = MediaCodeByTypeConstants::BULLETIN_ANNUAL;
        } elseif($schoolReport->getBacSup()->getType() === BacSupConstants::TYPE_SEMESTRIAL) {
            $list = MediaCodeByTypeConstants::BULLETIN_SEMESTRIAL;
        } else {
            throw new Exception('Invalid BacSup type');
        }

        $bacSup = $schoolReport->getBacSup();
        $mainsBacSup = $this->bacSupRepository->getMainsBacSup(cvId: $bacSup->getCv()->getId());
        $collection = new ArrayCollection($mainsBacSup);
        $existingSchoolReports = (new ArrayCollection($bacSup->getSchoolReports()->toArray()))->filter(function($s) {
            if (null !== $s->getId()) {
                return $s;
            }
        });
        $countExistingSchoolReports = $existingSchoolReports->count();

        $level = -1;

        if ($collection->contains($bacSup)) {
            $key = 0;
            foreach ($mainsBacSup as $main) {
                if ($main->getId() === $bacSup->getId()) {
                    $level = $key + (($bacSup->getType() === BacSupConstants::TYPE_ANNUAL)? 0 : $countExistingSchoolReports);
                    break;
                }
                $key += ($bacSup->getType() === BacSupConstants::TYPE_ANNUAL)? 1 : 2;
            }
        } else {
            $level = $collection->count();
            $level *= ($bacSup->getType() === BacSupConstants::TYPE_ANNUAL)? 1 : 2;
            // If bac sup is created with two school reports then add +1 to level
            $sr = new ArrayCollection($bacSup->getSchoolReports()->toArray());
            if ($sr->first() !== $schoolReport) {
                $level += 1;
            }
        }

        if(!isset($list[$level])) {
            throw new Exception('Media code constant not found');
        }

        return $list[$level];
    }
}
