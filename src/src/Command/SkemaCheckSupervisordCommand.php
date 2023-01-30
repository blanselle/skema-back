<?php

namespace App\Command;

use Symfony\Component\Mime\Email;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'skema:check-supervisord',
    description: 'test supervisor status',
)]
class SkemaCheckSupervisordCommand extends Command
{
        public function __construct(
        #[Autowire('%backoffice_url%')]
        private string $backofficeUrl,
        #[Autowire('%supervision_sender%')]
        private string $sender,
        #[Autowire('%supervision_mail%')]
        private string $supervisionMail,
        private MailerInterface $mailer,
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $curl = new CurlHttpClient();
        $response = $curl->request('GET', 'http://localhost:9001', [
        ]);
        
        if($response->getStatusCode() != 200){
            $io->error('the status code is not 200. It is '.$response->getStatusCode());
            $this->sendErrorMail();

            return Command::FAILURE;
        }

        $crawler = new Crawler($response->getContent());

        foreach($crawler->filter('body > div > div > form > table > tbody > tr') as $tr){
            $element = new Crawler($tr);
            $statusRunning = $element->filter('td.status')->text();
            $worker = $element->filter('tr > td')->eq(2)->filter('a')->eq(0)->text();

            if($statusRunning !== 'running'){
                $this->sendErrorMail(worker: $worker);
                $io->error("{$worker} is down");
                break;
            }
        }
        $io->success('the CURL HTTP call is done');

        return Command::SUCCESS;
    }

    public function sendErrorMail(?string $worker = null): void
    {
        $text = 'Impossible de contacter le supervisord. Merci de vérifier immédiatement le fonctionnement';
        $html = '<p>Impossible de contacter le supervisord. Merci de vérifier immédiatement le fonctionnement</p>';
        if (null !== $worker) {
            $text = "le statut du process supervisord $worker n\'est pas running. Merci de vérifier immédiatement le fonctionnement";
            $html = '<p>le statut du process supervisord ' . $worker .'n\'est pas running. Merci de vérifier immédiatement le fonctionnement</p>';

        }
        $email = (new Email())
            ->from($this->sender)
            ->subject('warning supervisord status on : '.$this->backofficeUrl)
            ->text($text)
            ->html($html);

        $mailsTo = explode(';', $this->supervisionMail);
        foreach ($mailsTo as $mail) {
            $email->addTo($mail);
        }

        $this->mailer->send($email);
    }
}
