<?php

namespace App\Manager;

use App\Constants\CV\BacSupConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\SchoolReport;
use App\Repository\CV\BacSupRepository;

class BacSupManager
{
    public function __construct(private BacSupRepository $bacSupRepository) {}

    public function initIdentifiersByBacSups(array $bacSups): void
    {
        // Perform Identifier for non dual
        $i = 1;
        /** @var BacSup $bacSup */
        foreach ($bacSups as $bacSup) {
            $identifier = match ($i) {
                1 => BacSupConstants::BAC_PLUS_1,
                2 => BacSupConstants::BAC_PLUS_2,
                3 => BacSupConstants::BAC_PLUS_3,
                4 => BacSupConstants::BAC_PLUS_4,
                5 => BacSupConstants::BAC_PLUS_5,
                default => ''
            };
            if (empty($bacSup->getDualPathBacSup())) {
                $bacSup->setIdentifier($identifier);
                $i++;
            }
        }

        // Perform Identifier for dual
        /** @var BacSup $bacSup */
        foreach ($bacSups as $bacSup) {
            if (!empty($bacSup->getDualPathBacSup())) {
                $identifier = $bacSup->getDualPathBacSup()->getIdentifier();
                $bacSup->setIdentifier($identifier);
            }
        }
    }

    public function getIdentifier(Cv $cv): ?string
    {
        $existingBacSups = $this->bacSupRepository->getMainsBacSup(cvId: $cv->getId());
        $i = count($existingBacSups) + 1;

        return match ($i) {
            1 => BacSupConstants::BAC_PLUS_1,
            2 => BacSupConstants::BAC_PLUS_2,
            3 => BacSupConstants::BAC_PLUS_3,
            4 => BacSupConstants::BAC_PLUS_4,
            5 => BacSupConstants::BAC_PLUS_5,
            default => ''
        };
    }

    public function getSchoolReportMediaCode(Cv $cv): string
    {
        $code = "";
        $i = 1;
        /** @var BacSup $bacSup */
        foreach ($cv->getBacSups() as $bacSup) {
            $j = 1;
            /** @var SchoolReport $schoolReport */
            foreach ($bacSup->getSchoolReports() as $schoolReport) {
                    if ($bacSup->getType() == BacSupConstants::TYPE_ANNUAL) {
                        $code = match ($i) {
                            1 => MediaCodeConstants::CODE_BULLETIN_L1,
                            2 => MediaCodeConstants::CODE_BULLETIN_L2,
                            3 => MediaCodeConstants::CODE_BULLETIN_L3,
                            4 => MediaCodeConstants::CODE_BULLETIN_M1,
                            default => MediaCodeConstants::CODE_BULLETIN_M2
                        };
                    } elseif ($bacSup->getType() == BacSupConstants::TYPE_SEMESTRIAL) {
                        if ($i == 1) {
                            if ($j == 1) {
                                $code = MediaCodeConstants::CODE_BULLETIN_L1_S1;
                            } else {
                                $code = MediaCodeConstants::CODE_BULLETIN_L1_S2;
                            }
                        } elseif ($i == 2) {
                            if ($j == 1) {
                                $code = MediaCodeConstants::CODE_BULLETIN_L2_S3;
                            } else {
                                $code = MediaCodeConstants::CODE_BULLETIN_L2_S4;
                            }
                        } elseif ($i == 3) {
                            if ($j == 1) {
                                $code = MediaCodeConstants::CODE_BULLETIN_L3_S5;
                            } else {
                                $code = MediaCodeConstants::CODE_BULLETIN_L3_S6;
                            }
                        } elseif ($i == 4) {
                            if ($j == 1) {
                                $code = MediaCodeConstants::CODE_BULLETIN_M1_S1;
                            } else {
                                $code = MediaCodeConstants::CODE_BULLETIN_M1_S2;
                            }
                        } elseif ($i == 5) {
                            if ($j == 1) {
                                $code = MediaCodeConstants::CODE_BULLETIN_M2_S3;
                            } else {
                                $code = MediaCodeConstants::CODE_BULLETIN_M2_S4;
                            }
                        }
                    }
                $j++;
            }
            $i++;
        }
        return $code;
    }
}
