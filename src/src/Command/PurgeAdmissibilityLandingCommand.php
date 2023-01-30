<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Manager\Admissibility\LandingPage\PurgeVarnishManager;

#[AsCommand(
    name: 'app:landing-admissibility:purge',
    description: 'Purge all admissibility landing page on Varnish.'
)]
class PurgeAdmissibilityLandingCommand extends Command
{
    public function __construct(
        private PurgeVarnishManager $purger,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->purger->execute();

        return Command::SUCCESS;
    }
}
