<?php

namespace App\Helper;

use App\Entity\Student;
use App\Manager\ParameterManager;
use App\Service\Bloc\BlocRewriter;
use DateTime;
use Exception;
use Psr\Cache\CacheException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheHelper
{
    public const ADMISSIBILITY_RESULT_OK = 'ADMISSIBILITY_RESULT_OK';
    public const ADMISSIBILITY_RESULT_KO = 'ADMISSIBILITY_RESULT_KO';
    private const DATE_LIMIT_PARAMETER_KEY = 'dateResultatsAdmissibilite';
    private const CACHE_KEY_ADMISSIBILITY_RESULT = 'admissibility_result_%student_identifier%';
    private const CACHE_KEY_DATE_RESULTATS__ADMISSIBILITE = self::DATE_LIMIT_PARAMETER_KEY."_%program_channel_id%";
    private const CACHE_KEY_PUBLIC_HOLIDAYS = "public_holidays_%year%";

    public function __construct(
        private CacheInterface $cache,
        private BlocRewriter $blocRewriter,
        private ParameterManager $parameterManager,
        private CalendrierGouvHelper $calendrierGouvHelper
    ) {}

    public function getDateResultatsAdmissibilite(Student $student): DateTime|false
    {
        $programChannel = $student->getProgramChannel();

        return DateTime::createFromFormat('Y-m-d H:i', $this->cache->get(
            str_replace('%program_channel_id%', (string)$programChannel->getId(), self::CACHE_KEY_DATE_RESULTATS__ADMISSIBILITE),
            function(ItemInterface $item) use ($programChannel) {
                $item->expiresAfter(3600);
                $parameter = $this->parameterManager->getParameter(self::DATE_LIMIT_PARAMETER_KEY, $programChannel);

                return $parameter->getValue()->format('Y-m-d H:i');
        }));
    }

    public function getAdmissibilityResult(Student $student, string $key): ?string
    {
        $cached = null;
        try {
            $cached = $this->cache->get(
                str_replace('%student_identifier%', $student->getIdentifier(), self::CACHE_KEY_ADMISSIBILITY_RESULT),
                function (ItemInterface $item) use ($student, $key) {
                    $item->expiresAfter(3600);
                    $bloc = $this->blocRewriter->rewriteBloc(bloc: $key, programChannel: $student->getProgramChannel());

                    return $bloc->getContent();
                }
            );
        } catch (Exception $e) {
            // empty
        }

        return $cached;
    }

    public function getPublicHolidays(string $year): array
    {
        $cacheKey = str_replace('%year%', $year, self::CACHE_KEY_PUBLIC_HOLIDAYS);
        $cached = [];

        try {
            $cached = $this->cache->get(
                $cacheKey,
                function (ItemInterface $item) use ($year) {
                    $item->expiresAfter(3600 * 24);
                    return $this->calendrierGouvHelper->getPublicHolidays(year: $year);
                }
            );
        } catch (CacheException|Exception $e) {
            // empty
        }

        // Force retry if cache is empty
        if (empty($cached)) {
            try {
                $this->cache->delete(key: $cacheKey);
            } catch (CacheException|Exception $e) {
                // empty
            }
        }

        return $cached;
    }
}