<?php

declare(strict_types=1);

namespace App\Security\Traits;

use App\Entity\Student;
use App\Manager\ParameterManager;
use App\Service\Bloc\BlocRewriter;
use DateTime;

trait EndSubscriptionVoterTrait
{
    private ParameterManager $parameterManager;
    
    private BlocRewriter $blocRewriter;

    /**
     * @required
     * @return static
     */
    public function withParameterManager(
        ParameterManager $parameterManager,
    ): self 
    {
        $new = clone $this;
        $new->parameterManager = $parameterManager;
        
        return $new;
    }

    /**
     * @required
     * @return static
     */
    public function withBlocRewriter(
        BlocRewriter $blocRewriter, 
    ): self 
    {
        $new = clone $this;
        $new->blocRewriter = $blocRewriter;
        
        return $new;
    }
    
    private function getDate(Student $student): DateTime
    {
        return $this->parameterManager->getParameter('dateClotureInscriptions', $student->getProgramChannel())->getValue();
    }

    private function getDateFinCV(Student $student): DateTime
    {
        return $this->parameterManager->getParameter('dateFinCV', $student->getProgramChannel())->getValue();
    }

    private function getMessageError(Student $student): string
    {
        return (string)$this->blocRewriter->rewriteBloc('REGISTRATIONS_CLOSED_CV_IN_PROGRESS_OF_CONTROL', $student->getProgramChannel())->getContent();
    }
}
