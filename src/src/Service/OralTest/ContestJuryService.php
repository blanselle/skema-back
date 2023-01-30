<?php

namespace App\Service\OralTest;

use App\Entity\OralTest\ExamPeriod;
use App\Entity\OralTest\ExamTest;
use App\Entity\OralTest\Jury;
use App\Entity\OralTest\PlanningInfo;
use App\Exception\Sudoku\ContestJuryClientException;
use App\Helper\ContestJuryHelper;
use App\Repository\Exam\ExamLanguageRepository;
use App\Repository\OralTest\PlanningInfoRepository;
use App\Repository\OralTest\SlotTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class ContestJuryService
{
    public function __construct(
        private ContestJuryHelper $contestJuryHelper,
        private EntityManagerInterface $entityManager,
        private PlanningInfoRepository $planningInfoRepository,
        private ExamLanguageRepository $examLanguageRepository,
        private SlotTypeRepository $slotTypeRepository
    ) {}

    /**
     * @throws ContestJuryClientException
     */
    public function getPlanningInfo(string $contestJuryWebsiteCode, \DateTimeImmutable $date): ?PlanningInfo
    {
        $planningInfoData = $this->contestJuryHelper->getPlanningInfo($contestJuryWebsiteCode, $date);
        $planningInfo = $this->planningInfoRepository->findOneBy(['contestJuryWebsiteCode' => $contestJuryWebsiteCode, 'date' => $date]);

        if (null !== $planningInfo) {
            $this->entityManager->remove($planningInfo);
            $this->entityManager->flush();
        }

        if (empty($planningInfoData)) {
            return null;
        }

        return $this->processPlanningInfo($contestJuryWebsiteCode, $date, $planningInfoData);
    }

    private function processPlanningInfo(string $contestJuryWebsiteCode, \DateTimeImmutable $date, array $data): PlanningInfo
    {
        $examsLanguage = $this->examLanguageRepository->findAll();
        $examsLanguageList = [];
        foreach ($examsLanguage as $examLanguage) {
            $examsLanguageList[$examLanguage->getKey()] = $examLanguage;
        }

        $slotsType = $this->slotTypeRepository->findAll();
        $slotsTypeList = [];
        foreach ($slotsType as $slotType) {
            $slotsTypeList[$slotType->getCode()] = $slotType;
        }

        $planningInfo = (new PlanningInfo())
            ->setContestJuryWebsiteCode($contestJuryWebsiteCode)
            ->setDate($date)
        ;

        foreach ($data as $codeLang => $examPeriodData) {
            $examTest = (new ExamTest())
                ->setExamLanguage($examsLanguageList[$codeLang])
            ;
            $planningInfo->addExamTest($examTest);

            foreach ($examPeriodData as $period => $juryData) {
                $examPeriod = (new ExamPeriod())
                    ->setSlotType($slotsTypeList[$period])
                    ->setNbOfJuries($juryData['nbJurys'])
                ;
                $examTest->addExamPeriode($examPeriod);

                foreach ($juryData['jurys'] as $code => $juryDatum) {
                    $jury = (new Jury())
                        ->setCode($code)
                        ->setClassRoomNumber($juryDatum['salle'])
                        ->setExaminers($juryDatum['examinateurs'])
                    ;
                    $examPeriod->addJury($jury);
                }
            }
        }

        $this->entityManager->persist($planningInfo);
        $this->entityManager->flush();

        return $planningInfo;
    }
}