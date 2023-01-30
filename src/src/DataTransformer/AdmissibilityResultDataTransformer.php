<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Dto\AdmissibilityResultOutput;
use App\Entity\Student;
use App\Helper\CacheHelper;
use DateTime;
use Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AdmissibilityResultDataTransformer implements DataTransformerInterface
{
    private const DATE_FORMAT = 'd MMMM y à HH:mm';

    public function __construct(private CacheHelper $cacheHelper) {}
    
    public function transform($object, string $to, array $context = []): object
    {
        /** @var Student $object */

        $dateLimit = $this->cacheHelper->getDateResultatsAdmissibilite(student: $object);

        if($dateLimit === false) {
            throw new Exception('[Résultat d\'admissibilité] La date des résultats d\'admissibilité n\'a pas été trouvée.');
        }

        $now = new DateTime();
        if($now < $dateLimit) {
            throw new AccessDeniedException(message: sprintf(
                'Ces informations seront disponibles à partir du %s',
                \IntlDateFormatter::formatObject(
                    $dateLimit,
                    self::DATE_FORMAT,
                )
            ));
        }

        $output = new AdmissibilityResultOutput();
        $output->score = $object->getAdmissibilityGlobalScore();
        $output->scoreMax = $object->getAdmissibilityMaxScore();
        $output->admissible = ($object->getState() === StudentWorkflowStateConstants::STATE_ADMISSIBLE);
        
        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return AdmissibilityResultOutput::class === $to && $data instanceof Student;
    }
}
