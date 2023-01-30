<?php

declare(strict_types=1);

namespace App\Constants\Media;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class MediaCodeConstants implements ConstantsInterface
{
    public const CODE_CERTIFICAT_ELIGIBILITE = 'certificat_eligibilite';
    public const CODE_CERTIFICAT_DOUBLE_PARCOURS = 'certificat_double_parcours';
    public const CODE_SHN = 'shn';
    public const CODE_TT = 'tt';
    public const CODE_CROUS = 'crous';
    public const CODE_ID_CARD = 'id_card';
    public const CODE_BAC = 'bac';
    public const CODE_ATTESTATION_ANGLAIS = 'attestation_anglais';
    public const CODE_ATTESTATION_MANAGEMENT = 'attestation_management';
    public const CODE_JOURNEE_DEFENSE_CITOYENNE = 'jdc';
    public const CODE_AUTRE = 'autre';
    public const CODE_SUMMON = 'summon';

    public const CODE_BULLETIN_L1_S1 = 'bulletin_L1_S1';
    public const CODE_BULLETIN_L1_S2 = 'bulletin_L1_S2';
    public const CODE_BULLETIN_L2_S3 = 'bulletin_L2_S3';
    public const CODE_BULLETIN_L2_S4 = 'bulletin_L2_S4';
    public const CODE_BULLETIN_L3_S5 = 'bulletin_L3_S5';
    public const CODE_BULLETIN_L3_S6 = 'bulletin_L3_S6';
    public const CODE_BULLETIN_M1_S1 = 'bulletin_M1_S1';
    public const CODE_BULLETIN_M1_S2 = 'bulletin_M1_S2';
    public const CODE_BULLETIN_M2_S3 = 'bulletin_M2_S3';
    public const CODE_BULLETIN_M2_S4 = 'bulletin_M2_S4';
    public const CODE_BULLETIN_L1 = 'bulletin_L1';
    public const CODE_BULLETIN_L2 = 'bulletin_L2';
    public const CODE_BULLETIN_L3 = 'bulletin_L3';
    public const CODE_BULLETIN_M1 = 'bulletin_M1';
    public const CODE_BULLETIN_M2 = 'bulletin_M2';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }

    public static function getBulletins(): array
    {
        $codeBulletins = [];
        foreach(self::getConsts() as $key => $code) {
            if(str_contains($key, 'CODE_BULLETIN_')) {
                $codeBulletins[$key] = $code;
            }
        }

        return $codeBulletins;
    }
}
