<?php

declare(strict_types=1);

namespace App\Controller\Notification;

use App\Entity\User;
use App\Entity\Student;
use App\Service\Datatable;
use App\Manager\NotificationManager;
use App\Repository\StudentRepository;
use App\Form\Notification\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Constants\User\UserRoleConstants;
use App\Entity\Notification\Notification;
use App\Form\Notification\NotificationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Notification\NotificationReplyType;
use App\Service\Notification\NotificationCenter;
use App\Constants\Notification\NotificationConstants;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route('/admin/notifications')]
#[IsGranted('ROLE_COORDINATOR')]
class NotificationController extends AbstractController
{
    private const NOTIFICATION_TYPE_COMMENT = 'comment';

    public function __construct(
        private Security $security,
        private NotificationCenter $notificationCenter,
        #[Autowire('%env(resolve:BACKOFFICE_URL)%')]
        private string $backOfficeUrl
    ) {
    }

    #[Route('', name: 'notification_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, Datatable $datatable): Response
    {
        $student = null;
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'identifier'    => $datatable->filter('identifier'),
            'subject'       => $datatable->filter('subject'),
            'student'       => $datatable->filter('student'),
            'comment'       => $datatable->filter('comment'),
            'firstname'       => $datatable->filter('firstname'),
            'lastname'       => $datatable->filter('lastname'),
        ]);

        $data['columns'] = [
            'identifier'        => ['db' => 'a.sender', 'label' => "Expéditeur"],
            'subject'  => ['db' => 'a.subject', 'label' => "Sujet"],
            'createdAt'  => ['db' => 'a.createdAt', 'label' => "Date"],
            'state'     => ['db' => 'a.read', 'label' => "État"],
            'programChannel' => ['db' => 'sender_pc.name', 'label' => "Voie de concours"],
            'comment' => ['label' => "Commentaire interne"],
            'action'    => ['label' => "Action"],
        ];
        $data['user'] = $this->security->getUser();

        if (!empty($datatable->filter('student'))) {
            $student = $em->getRepository(Student::class)->findOneBy(['identifier' => $datatable->filter('student')]);
            $data['student'] = $student;
        }

        return $datatable->getDatatableResponse($request, Notification::class, $data, 'notification/notification', [
            'student' => $student,
        ]);
    }

    #[Route('/send', name: 'notification_send', methods: ['GET', 'POST'])]
    public function send(Request $request, EntityManagerInterface $em, Datatable $datatable): Response
    {
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'identifier'    => $datatable->filter('identifier'),
            'receiver'        => $datatable->filter('receiver'),
            'subject'       => $datatable->filter('subject'),
            'student'       => $datatable->filter('student'),
            'comment'       => $datatable->filter('comment'),
            'firstname'       => $datatable->filter('firstname'),
            'lastname'       => $datatable->filter('lastname'),
        ]);

        $data['columns'] = [
            'identifier'        => ['db' => 'a.sender', 'label' => "Expéditeur"],
            'receiver' => ['db' => 'a.receiver', 'label' => "Destinataire"],
            'subject'  => ['db' => 'a.subject', 'label' => "Sujet"],
            'createdAt'  => ['db' => 'a.createdAt', 'label' => "Date"],
            'state'     => ['db' => 'a.read', 'label' => "État"],
            'programChannel' => ['db' => 'sender_pc.name', 'label' => "Voie de concours"],
            'comment' => ['label' => "Commentaire interne"],
            'action'    => ['label' => "Action"],
        ];
                
        $data['sender'] = $this->security->getUser();
        
        return $datatable->getDatatableResponse($request, Notification::class, $data, 'notification/notification_list', [
            'student' => [],
        ]);
    }

    #[Route('/new', name: 'notification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $student = null;
        $role = null;
        $notification = new Notification();
        if ($request->query->has('type') && $request->query->get('type') == self::NOTIFICATION_TYPE_COMMENT) {
            $notification
                ->setSubject('Commentaire')
                ->setPrivate(true)
            ;
        }
        if ($request->query->has('student')) {
            $student = $em->getRepository(Student::class)->findOneBy(['identifier' => $request->query->get('student')]);
            $notification->setReceiver($student->getUser());
            $notification->setIdentifier($student->getIdentifier());
        }
        if ($request->query->has('role')) {
            switch ($request->query->get('role')) {
                case 'coordinateur':
                    $role = UserRoleConstants::ROLE_COORDINATOR;
            }

            $notification->setRoles([$role]);
            $notification->setRead(true);
        }

        $form = $this->createForm(NotificationType::class, $notification, [
            'canModifySubject' => ($request->query->has('type') && $request->query->get('type') == self::NOTIFICATION_TYPE_COMMENT),
            'canModifyReceiver' => (!$request->query->has('type') && !$request->query->has('student') && !$request->query->has('role')),
        ]);
        if ($request->query->has('student')) {
            $form->remove('programChannels');
        } elseif ($request->query->has('role')) {
            $form->remove('programChannels');
        }
        if (!$request->query->has('role')) {
            $form->remove('roles');
        }
        if ($request->query->has('type') && $request->query->get('type') == self::NOTIFICATION_TYPE_COMMENT) {
            $title = 'notification.page.title.comment';
            $form->remove('receiver');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var User $sender
             */
            $sender = $this->security->getUser();
            $notification->setSender($sender);
            $notification->setRoleSender($sender->getRoles());

            // Due to disabled on select receiver
            if ($request->query->has('student')) {
                $student = $em->getRepository(Student::class)->findOneBy(['identifier' => $request->query->get('student')]);
                $notification->setReceiver($student->getUser());
                $notification->setIdentifier($student->getIdentifier());
            }

            $em->persist($notification);

            if ($request->query->has('type') && $request->query->get('type') === self::NOTIFICATION_TYPE_COMMENT) {
                $this->notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB], sendGenericMail: false);
            } elseif (!$request->query->has('type') || $request->query->get('type') !== self::NOTIFICATION_TYPE_COMMENT) {
                $this->notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB]);
            }

            if ($request->query->has('student') && ($request->query->has('type') && $request->query->get('type') === self::NOTIFICATION_TYPE_COMMENT)) {
                $this->addFlash('success', 'Le commentaire est déposé avec succès');
                return $this->redirectToRoute('student_edit', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
            }

            if ($request->query->has('student')) {
                return $this->redirectToRoute('notification_index', ['student' => $request->query->get('student')], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('notification_index', ['sender' => $request->query->get('student')], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notification/notification/new.html.twig', [
            'form' => $form,
            'student' => $student,
            'role' => $role,
            'title' => $title?? null,
        ]);
    }

    #[Route('/{id}/edit', name: 'notification_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        Notification $notification,
        StudentRepository $studentRepository,
        NotificationManager $notificationManager
    ): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        $params = [];
        $studentLink = null;
        if (null !== $referer && str_contains($referer, '/admin/notifications') && !str_contains($referer, 'send')) {
            $urlQueries = parse_url($referer, PHP_URL_QUERY);
            if (is_string($urlQueries)) {
                parse_str($urlQueries, $params);
            }
            if (!empty($params) && array_key_exists('student', $params)) {
                $student = $studentRepository->findOneBy(['identifier' => $params['student']]);
                if (!empty($student)) {
                    $studentLink = sprintf('%s/%s', rtrim($this->backOfficeUrl, '/'), ltrim($this->generateUrl('student_edit', ['id' => $student->getId()]), '/'));
                }
            }
        }
        if (null !== $referer && str_contains($referer, '/admin/notifications/send')) {
            $referer = sprintf('%s/%s', rtrim($this->backOfficeUrl, '/'), ltrim($this->generateUrl('notification_send'), '/'));
        }else{
            $referer = sprintf('%s/%s', rtrim($this->backOfficeUrl, '/'), ltrim($this->generateUrl('notification_index'), '/'));
        }

        $reply = new Notification();
        $reply->setSubject('Re : '.$notification->getSubject());
        $form = $this->createForm(NotificationReplyType::class, $reply);
        $form->handleRequest($request);

        $formComment = $this->createForm(CommentType::class, $notification);
        $formComment->handleRequest($request);

        $notificationParent = $notificationManager->getParentThread($notification);

        if ($request->isMethod('POST') && $request->request->has('treat_hidden')) {
            if ($request->request->has('treat')) {
                $notification->setRead(true);
                $em->flush();
            }
        } elseif ($form->isSubmitted() && $form->isValid()) {
            $reply->setParent($notificationParent);
            if (null != $this->security->getUser()) {
                /**
                 * @var User $user
                 */
                $user = $this->security->getUser();
                $reply->setSender($user);
                $reply->setRoleSender($user->getRoles());
                if (null != $notification->getSender() && in_array('ROLE_CANDIDATE', $notification->getSender()->getRoles(), true)) {
                    $notification->setRead(true);
                    $em->flush();
                }
            }
            $reply->setReceiver($notification->getSender());
            $this->notificationCenter->dispatch($reply, [NotificationConstants::TRANSPORT_DB]);
            return $this->redirectToRoute('notification_edit', ['id' => $notification->getId()], Response::HTTP_SEE_OTHER);
        } elseif ($formComment->isSubmitted() && $formComment->isValid()) {
            $em->flush();
        }

        /** @var Notification $notification */
        $notification = $em->getRepository(Notification::class)->find($notification->getId());

        return $this->renderForm('notification/notification/edit.html.twig', [
            'notificationParent' => $notificationParent,
            'notification' => $notification,
            'form' => $form,
            'formComment' => $formComment,
            'referer' => $referer,
            'studentLink' => $studentLink,
        ]);
    }
    #[Route('/{id}/treat', name: 'notification_treat', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function treat(
        Request $request,
        EntityManagerInterface $em,
        Notification $notification
    ): Response{
        $notification->setRead(true);
        $em->flush();
        $this->addFlash('success', 'Le commentaire est traité avec succès');
        $student = $request->request->get('identifier');
        
        /** @var Student $student */
        $student = $em->getRepository(student::class)->findOneBy(['identifier' => $student]);
        if(null === $student){
            throw new BadRequestHttpException("Invalid student");
        }
        return $this->redirectToRoute('student_edit', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);

    }
}
