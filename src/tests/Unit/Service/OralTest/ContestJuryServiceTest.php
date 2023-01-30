<?php

namespace App\Tests\Unit\Service\OralTest;

use App\Entity\Exam\ExamLanguage;
use App\Entity\OralTest\ExamPeriod;
use App\Entity\OralTest\ExamTest;
use App\Entity\OralTest\Jury;
use App\Entity\OralTest\PlanningInfo;
use App\Entity\OralTest\SlotType;
use App\Exception\Sudoku\ContestJuryClientException;
use App\Helper\ContestJuryHelper;
use App\Repository\Exam\ExamLanguageRepository;
use App\Repository\OralTest\PlanningInfoRepository;
use App\Repository\OralTest\SlotTypeRepository;
use App\Service\OralTest\ContestJuryService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContestJuryServiceTest extends TestCase
{
    private const CAMPUS_CODE = 'sophia';
    private const DATE = '2022-06-08';

    private EntityManagerInterface|MockObject $em;
    private PlanningInfoRepository|MockObject $planningInfoRepository;
    private ExamLanguageRepository|MockObject $examLanguageRepository;
    private SlotTypeRepository|MockObject $slotTypeRepository;
    private ContestJuryHelper|MockObject $contestJuryHelper;
    private ContestJuryService $contestJuryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->planningInfoRepository = $this->createMock(PlanningInfoRepository::class);
        $this->examLanguageRepository = $this->createMock(ExamLanguageRepository::class);
        $this->slotTypeRepository = $this->createMock(SlotTypeRepository::class);
        $this->contestJuryHelper = $this->createMock(ContestJuryHelper::class);
        $this->contestJuryService = new ContestJuryService($this->contestJuryHelper, $this->em, $this->planningInfoRepository, $this->examLanguageRepository, $this->slotTypeRepository);
    }

    public function testGetPlanningInfoWithData(): void
    {
        $content = '{"ALL":{"M":{"nbJurys":1,"jurys":{"D108":{"salle":"109","examinateurs":["HESTERMANN Marlène"]}}}},"ANG":{"M":{"nbJurys":3,"jurys":{"A108":{"salle":"113","examinateurs":["Camos Michel"]},"A208":{"salle":"112","examinateurs":["SCHALL CHRISTOPHER"]},"A308":{"salle":"111","examinateurs":["ONTENIENTE GAETAN"]}}},"A":{"nbJurys":3,"jurys":{"A108":{"salle":"113","examinateurs":["Camos Michel"]},"A208":{"salle":"112","examinateurs":["SCHALL CHRISTOPHER"]},"A308":{"salle":"111","examinateurs":["FASSI VERONICA"]}}}},"ENT":{"M":{"nbJurys":5,"jurys":{"E108":{"salle":"114","examinateurs":["Lavagna Pascal","AMYUNI Tarek Michel"]},"E208":{"salle":"115","examinateurs":["CHEREAU Philippe","Roussellier Pierre"]},"E308":{"salle":"116","examinateurs":["Roszak Sabrina","CHAFFARD-SAUZE CHRISTINE","DEPARDIEU Alexandre"]},"E408":{"salle":"117","examinateurs":["WAUTHIER Virginie","VIAN Dominique"]},"E508":{"salle":"118","examinateurs":["PLANQUE Alexis","DISPAS Christophe"]}}},"A":{"nbJurys":4,"jurys":{"E108":{"salle":"114","examinateurs":["AMYUNI Tarek Michel","CHAFFARD-SAUZE CHRISTINE"]},"E208":{"salle":"115","examinateurs":["CHEREAU Philippe","DEPARDIEU Alexandre"]},"E308":{"salle":"116","examinateurs":["Roszak Sabrina","Roussellier Pierre"]},"E408":{"salle":"117","examinateurs":["WAUTHIER Virginie","VIAN Dominique","Otmanine Irina"]}}}},"ESP":{"A":{"nbJurys":1,"jurys":{"S108":{"salle":"134","examinateurs":["GARCIA VICENTE JUDIT"]}}},"M":{"nbJurys":1,"jurys":{"S108":{"salle":"134","examinateurs":["DILLENSCHNEIDER CRISTINA"]}}}}}';

        $expectedResponseData = json_decode($content, true);
        $this->contestJuryHelper->expects($this->once())->method('getPlanningInfo')->willReturn($expectedResponseData);
        $this->planningInfoRepository->expects($this->any())->method('findOneBy')->willReturn(null);
        $examLanguageKeys = ['ALL', 'ANG', 'ENT', 'ESP'];
        $this->examLanguageRepository->expects($this->any())->method('findAll')->willReturn(array_map(function($key) {
            return (new ExamLanguage())
                ->setKey($key)
            ;
        }, $examLanguageKeys));
        $slotsTypeCodes = ['M', 'A'];
        $this->slotTypeRepository->expects($this->any())->method('findAll')->willReturn(array_map(function($code) {
            return (new SlotType())
                ->setCode($code)
            ;
        }, $slotsTypeCodes));
        $planningInfoExpected = $this->processPlanningInfo(self::CAMPUS_CODE, new \DateTimeImmutable(self::DATE), $expectedResponseData);

        $planningInfo = $this->contestJuryService->getPlanningInfo(self::CAMPUS_CODE, new \DateTimeImmutable(self::DATE));
        $this->assertSame($planningInfoExpected->getContestJuryWebsiteCode(), $planningInfo->getContestJuryWebsiteCode());
        $this->assertSame($planningInfoExpected->getDate()->format('Y-m-d'), $planningInfo->getDate()->format('Y-m-d'));
    }

    public function testGetPlanningInfoWithoutData(): void
    {
        $content = '{}';

        $expectedResponseData = json_decode($content, true);
        $this->contestJuryHelper->expects($this->once())->method('getPlanningInfo')->willReturn($expectedResponseData);
        $this->planningInfoRepository->expects($this->any())->method('findOneBy')->willReturn(null);

        $planningInfo = $this->contestJuryService->getPlanningInfo(self::CAMPUS_CODE, new \DateTimeImmutable(self::DATE));
        $this->assertNull($planningInfo);
    }

    public function testGetPlanningInfoWithClientError(): void
    {
        $this->contestJuryHelper->expects($this->once())->method('getPlanningInfo')->willThrowException(new ContestJuryClientException('Le service juryconcours ne réponds pas, le sudoku ne peut pas être initialisé'));
        $this->planningInfoRepository->expects($this->any())->method('findOneBy')->willReturn(null);

        $this->expectException(ContestJuryClientException::class);
        $planningInfo = $this->contestJuryService->getPlanningInfo(self::CAMPUS_CODE, new \DateTimeImmutable(self::DATE));
    }

    private function processPlanningInfo(string $contestJuryWebsiteCode, \DateTimeImmutable $date, array $data): PlanningInfo
    {
        $planningInfo = (new PlanningInfo())
            ->setContestJuryWebsiteCode($contestJuryWebsiteCode)
            ->setDate($date)
        ;

        foreach ($data as $codeLang => $examPeriodData) {
            $examTest = (new ExamTest())
                ->setExamLanguage((new ExamLanguage())->setName('test')->setKey($codeLang))
            ;
            $planningInfo->addExamTest($examTest);

            foreach ($examPeriodData as $period => $juryData) {
                $examPeriod = (new ExamPeriod())
                    ->setSlotType((new SlotType())->setCode($period))
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

        return $planningInfo;
    }
}