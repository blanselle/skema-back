<?php

declare(strict_types=1);

namespace App\Constants\Dashboard;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class DashboardMediaLabelConstants implements ConstantsInterface
{
    public const CERTIFICAT_ELIGIBILITE = 'Certificat de scolarité';
    public const CROUS = 'Attestation de bourses';
    public const SHN = 'Attestation SHN';
    public const TT = 'Attestation Tiers temps';
    public const BAC = 'Baccalauréat';
    public const BULLETIN = 'Relevé de notes';
    public const ATTESTATION_ANGLAIS = 'Attestation de résultats - Anglais hors Skema';
    public const ATTESTATION_MANAGEMENT = 'Attestation de résultats GMAT';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
