<?php

namespace App\Command;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Parameter\Parameter;
use App\Repository\Parameter\ParameterRepository;
use App\Repository\StudentRepository;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \DateTimeImmutable;

#[AsCommand(
    name: 'app:closing-of-registration',
    description: 'Update the student state after the date end of cv is passed.'
)]
class ClosingOfRegistrationCommand extends Command
{
    public function __construct(
        private LoggerInterface $crontabLogger,
        private ParameterRepository $parameterRepository,
        private StudentRepository $studentRepository,
        private StudentWorkflowManager $studentWorkflowManager,
        string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new DateTimeImmutable('now');
        $params = $this->parameterRepository->findParameterByKeyName('dateFinCV');

        /** @var Parameter $param */
        foreach ($params as $param) {
            $dateFinCv = $param->getValueDateTime();
            $programChannels = $param->getProgramChannels()->toArray();
            if ($dateFinCv < $now) {
                // spec 15.650 & 15.680
                $students = $this->studentRepository->fetchStudentsForClosingRegistration(
                    states: [
                        StudentWorkflowStateConstants::STATE_VALID,
                        StudentWorkflowStateConstants::STATE_ELIGIBLE,
                        StudentWorkflowStateConstants::STATE_CHECK_BOURSIER,
                        StudentWorkflowStateConstants::STATE_RECHECK_BOURSIER,
                    ],
                    programChannels:  $programChannels
                );
                $nbOfStudentsComplete = 0;
                foreach ($students as $student) {
                    if($this->studentWorkflowManager->complete($student)) {
                        $nbOfStudentsComplete++;
                    }
                }

                $this->crontabLogger->info(sprintf('Number of candidates with complete status %d / %d', $nbOfStudentsComplete, count($students)));

                // spec 15.670 & 15.690
                $students = $this->studentRepository->fetchStudentsForClosingRegistration(
                    states: [
                        StudentWorkflowStateConstants::STATE_CREATED_TO_PAY,
                    ],
                    programChannels:  $programChannels
                );
                $nbOfDeclinedPayment = 0;
                foreach ($students as $student) {
                    if ($this->studentWorkflowManager->declinedPayment($student)) {
                        $nbOfDeclinedPayment++;
                    }
                }

                $this->crontabLogger->info(sprintf('Number of candidates with declined_payment status %d / %d', $nbOfDeclinedPayment, count($students)));

                // spec 15.700
                $students = $this->studentRepository->fetchStudentsForClosingRegistration(
                    states: [
                        StudentWorkflowStateConstants::STATE_BOURSIER_KO,
                    ],
                    programChannels:  $programChannels
                );
                $nbOfCompleteProof = 0;
                foreach ($students as $student) {
                    if ($this->studentWorkflowManager->completeProof($student)) {
                        $nbOfCompleteProof++;
                    }
                }

                $this->crontabLogger->info(sprintf('Number of candidates with complete_proof status %d / %d', $nbOfCompleteProof, count($students)));
            }
        }

        return Command::SUCCESS;
    }
}