<?php

namespace App\Handler;

use ApiPlatform\Api\UrlGeneratorInterface;
use App\Constants\Notification\NotificationConstants;
use App\Entity\User;
use App\Manager\NotificationManager;
use App\Manager\StudentExportManager;
use App\Message\StudentExportListMessage;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationCenter;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler]
class StudentExportListHandler implements MessageHandlerInterface
{
    public function __construct(
        private StudentExportManager $studentExportManager,
        private NotificationCenter $notificationCenter,
        private NotificationManager $notificationManager,
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository,
        private LoggerInterface $exportLogger,
        #[Autowire('%backoffice_url%')]
        private string $backofficeUrl
    ) {}

    public function __invoke(StudentExportListMessage $message): void
    {
        $filename = sprintf('export-student-list-%s', date('YmdHis'));

        $this->exportLogger->info("Request student list export {$filename}");

        try {
            $file = $this->studentExportManager->export(filename: $filename, model: $message->getModel());
            $pos = strrpos($file, '/');
            $id = $pos === false ? $file : substr($file, $pos + 1);
            /** @var User $user */
            $user = $this->userRepository->find($message->getUserId());

            $link = sprintf('%s/%s', rtrim($this->backofficeUrl, '/'), ltrim($this->urlGenerator->generate(name: 'admin_student_export_list_file', parameters: ['filename' => $id]), '/'));

            $this->exportLogger->info(sprintf('File is available here %s', $link));

            $notification = $this->notificationManager->createNotification(
                receiver: $user,
                blocKey: 'STUDENT_EXPORT_LIST_NOTIFICATION',
                params: ['link' => $link]
            );

            $this->notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB]);
        } catch (\Exception $e) {
            $this->exportLogger->error($e->getMessage());
        }
    }
}