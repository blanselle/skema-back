<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Constants\Notification\NotificationConstants;
use App\Entity\Bloc\Bloc;
use App\Entity\Notification\Notification;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Exception\Bloc\BlocNotFoundException;
use App\Manager\Admissibility\LandingPage\TokenManager;
use App\Repository\BlocRepository;
use App\Service\Bloc\BlocRewriter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class NotificationAdmissibilityResultDispatcher
{
    private array $blocs = [];

    public function __construct(
        #[Autowire('%admissibility_landing_url%')]
        private string $admissibilityLandingUrl,
        private NotificationCenter $dispatcher,
        private BlocRewriter $blocRewriter,
        private BlocRepository $blocRepository,
        private TokenManager $tokenManager,

    ) {
    }

    public function dispatch(Student $student): void
    {
        $admissibilityStudentToken = $this->tokenManager->saveToken($student);

        $bloc = $this->blocRewriter->rewriteBloc(
            bloc: $this->getBlocByProgramChannel($student->getProgramChannel()), 
            programChannel: $student->getProgramChannel(),
            params: [
                'firstname' => $student->getUser()->getFirstName(),
                'link' => sprintf(
                    '%s?token=%s', 
                    $this->admissibilityLandingUrl,
                    $admissibilityStudentToken->getToken(),
                ),
            ]
        );
        
        $notification = (new Notification())
            ->setContent($bloc->getContent())
            ->setSubject($bloc->getLabel())
            ->setReceiver($student->getUser())
        ;

        $this->dispatcher->dispatch(
            $notification, 
            [NotificationConstants::TRANSPORT_EMAIL], 
            sendGenericMail: false
        );
    }

    /**
     * Keep bloc in memory in order to limit the number of sql request
     *
     * @param ProgramChannel $programChannel
     * @return Bloc
     */
    private function getBlocByProgramChannel(ProgramChannel $programChannel): Bloc
    {
        if(!isset($this->blocs[$programChannel->getId()])) {
            $bloc = $this->blocRepository->findActiveByKeyAndProgramChannel(key: 'MAIL_RESULTAT_ADMISSIBILITE', programChannel: $programChannel);

            if(null === $bloc) {
                throw new BlocNotFoundException('MAIL_RESULTAT_ADMISSIBILITE');
            }

            $this->blocs[$programChannel->getId()] = $bloc;
        }

        return $this->blocs[$programChannel->getId()];
    }
}
