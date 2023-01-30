<?php

declare(strict_types=1);

namespace App\Constants\Parameters;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class ParametersKeyConstants implements ConstantsInterface
{
    public const DATE_INSCRIPTION_START = 'dateDebutInscriptions';
    public const DATE_INSCRIPTION_END = 'dateClotureInscriptions';
    public const DATE_NAISSANCE_MAX = 'naissance_max';
    public const DATE_OUVERTURE_RDV = 'dateOuvertureRDV';
    public const DATE_RESULTATS_ADMISSION = 'dateResultatsAdmission';
    public const ANNE_CONCOURS = 'anneeConcours';
    public const ORAL_DATE_DEBUT = 'oralDateDebut';
    public const ORAL_DATE_FIN = 'oralDateFin';
    public const DATE_FERMETURE_RDV = 'dateFermetureRDV';
    public const LIMITE_RDV = 'limiteRDV';


    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
