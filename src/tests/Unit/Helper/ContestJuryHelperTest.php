<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helper;

use App\Helper\ContestJuryHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ContestJuryHelperTest extends TestCase
{
    private const CAMPUS_CODE = 'sophia';
    private const DATE = '2022-06-08';
    private const URL_JURY_CONCOURS = 'http://juryconcours.skema-bs.fr/';
    private const PATH_PLANNING_INFO = '/admin/planning/info';

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetPlanningInfoOk(): void
    {
        $content = '{"ALL":{"M":{"nbJurys":1,"jurys":{"D108":{"salle":"109","examinateurs":["HESTERMANN Marlène"]}}}},"ANG":{"M":{"nbJurys":3,"jurys":{"A108":{"salle":"113","examinateurs":["Camos Michel"]},"A208":{"salle":"112","examinateurs":["SCHALL CHRISTOPHER"]},"A308":{"salle":"111","examinateurs":["ONTENIENTE GAETAN"]}}},"A":{"nbJurys":3,"jurys":{"A108":{"salle":"113","examinateurs":["Camos Michel"]},"A208":{"salle":"112","examinateurs":["SCHALL CHRISTOPHER"]},"A308":{"salle":"111","examinateurs":["FASSI VERONICA"]}}}},"ENT":{"M":{"nbJurys":5,"jurys":{"E108":{"salle":"114","examinateurs":["Lavagna Pascal","AMYUNI Tarek Michel"]},"E208":{"salle":"115","examinateurs":["CHEREAU Philippe","Roussellier Pierre"]},"E308":{"salle":"116","examinateurs":["Roszak Sabrina","CHAFFARD-SAUZE CHRISTINE","DEPARDIEU Alexandre"]},"E408":{"salle":"117","examinateurs":["WAUTHIER Virginie","VIAN Dominique"]},"E508":{"salle":"118","examinateurs":["PLANQUE Alexis","DISPAS Christophe"]}}},"A":{"nbJurys":4,"jurys":{"E108":{"salle":"114","examinateurs":["AMYUNI Tarek Michel","CHAFFARD-SAUZE CHRISTINE"]},"E208":{"salle":"115","examinateurs":["CHEREAU Philippe","DEPARDIEU Alexandre"]},"E308":{"salle":"116","examinateurs":["Roszak Sabrina","Roussellier Pierre"]},"E408":{"salle":"117","examinateurs":["WAUTHIER Virginie","VIAN Dominique","Otmanine Irina"]}}}},"ESP":{"A":{"nbJurys":1,"jurys":{"S108":{"salle":"134","examinateurs":["GARCIA VICENTE JUDIT"]}}},"M":{"nbJurys":1,"jurys":{"S108":{"salle":"134","examinateurs":["DILLENSCHNEIDER CRISTINA"]}}}}}';

        $expectedResponseData = json_decode($content, true);


        $responses = [
            new MockResponse($content, [
                    'http_code' => 200,
                    'response_headers' => ['Content-Type: application/json'],
                ]
            ),
            new MockResponse('{}', [
                    'http_code' => 200,
                    'response_headers' => ['Content-Type: application/json'],
                ]
            ),
        ];
        $httpClient = new MockHttpClient($responses, self::getUrl(self::PATH_PLANNING_INFO));
        $helper = new ContestJuryHelper($httpClient);
        $response1 = $helper->getPlanningInfo(self::CAMPUS_CODE, new \DateTimeImmutable(self::DATE));
        $response2 = $helper->getPlanningInfo(self::CAMPUS_CODE, new \DateTimeImmutable(self::DATE));

        self::assertSame($response1, $expectedResponseData);
        self::assertSame($response2, []);
    }

    private static function getUrl(string $path): string
    {
        return self::URL_JURY_CONCOURS . ltrim($path, '/');
    }
}