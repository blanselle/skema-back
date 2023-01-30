<?php

declare(strict_types=1);

namespace App\Manager\Admissibility\LandingPage;

use ApiPlatform\HttpCache\VarnishPurger;
use App\Entity\Admissibility\LandingPage\AdmissibilityPurgeVarnish;
use App\Repository\Admissibility\LandingPage\AdmissibilityStudentTokenRepository;
use App\Repository\Admissibility\LandingPage\AdmissibilityPurgeVarnishRepository;
use App\Repository\Parameter\ParameterRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

class PurgeVarnishManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private ParameterRepository $parameterRepository,
        private AdmissibilityStudentTokenRepository $admissibilityStudentTokenRepository,
        private AdmissibilityPurgeVarnishRepository $admissibilityPurgeVarnishRepository,
        private VarnishPurger $purger,
    ) {}

    public function execute(): void
    {
        $alreadyPurged = false;
        $now = (new DateTime());

        $parameters = $this->parameterRepository->findParameterByKeyName('dateResultatsAdmissibilite');

        foreach($parameters as $parameter) {

            /** @var DateTimeInterface $date */
            $date = $parameter->getValue();
            
            foreach($parameter->getProgramChannels() as $programChannel) {

                $admissibilityPurgeVarnish = $this->admissibilityPurgeVarnishRepository->findOneBy(['programChannel' => $programChannel], []);

                if($admissibilityPurgeVarnish === null) {
                    $admissibilityPurgeVarnish = (new AdmissibilityPurgeVarnish())
                        ->setProgramChannel($programChannel)
                    ;
                    $this->em->persist($admissibilityPurgeVarnish);
                }

                if($now < $date) {

                    $admissibilityPurgeVarnish->setState(false);
                    $this->em->flush();
                    continue;
                }

                if($alreadyPurged === false) {

                    $tokens = $this->admissibilityStudentTokenRepository->getAllTokens();
                    foreach($tokens as $token) {
                        $this->purger->purge([sprintf('/api/students/landing_admissibility_publication?token=%s', $token['token'])]);
                    }

                    $alreadyPurged = true;
                }

                $admissibilityPurgeVarnish->setState(false);
                $this->em->flush();
            }
        }
    }
}