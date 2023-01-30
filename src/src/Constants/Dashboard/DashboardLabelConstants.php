<?php

declare(strict_types=1);

namespace App\Constants\Dashboard;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class DashboardLabelConstants implements ConstantsInterface
{
    public const NB_TOTAL = 'Nombre total d\'inscrits';
    public const START = 'Compte non activé';
    public const INITIALIZED_DEROGED = 'Candidature en dérogation';
    public const INITIALIZED_CONTROL_ELIGIBLE = 'Candidature initialisée - contrôle éligibilité';
    public const INITIALIZED = 'Candidature initialisée';
    public const IN_PROGRESS = 'Candidature en cours';
    public const VALIDATED = 'Candidature validée candidat';
    public const APPROVED = 'Candidature approuvée';
    public const REFUSED = 'Candidature refusée';
    public const ABORTED = 'Candidature annulée';
    public const RESIGNATION = 'Démission';
    public const DECLINED = 'Candidature non payée';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
