<?php

declare(strict_types=1);

namespace App\Controller\Notification;

use App\Entity\Notification\NotificationTemplate;
use App\Form\Notification\NotificationTemplateType;
use App\Repository\Notification\NotificationTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/notifications_template')]
#[IsGranted('ROLE_RESPONSABLE')]
class NotificationTemplateController extends AbstractController
{
    #[Route('', name: 'notification_template_index', methods: ['GET', 'POST'])]
    public function index(Request $request, NotificationTemplateRepository $notificationTemplateRepo, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $notificationTemplateRepo->findBy([], ['subject' => 'ASC']),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('notification/notification_template/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'notification_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notificationTemplate = new NotificationTemplate();
        $form = $this->createForm(NotificationTemplateType::class, $notificationTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($notificationTemplate);
            $entityManager->flush();

            return $this->redirectToRoute('notification_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notification/notification_template/new.html.twig', [
            'item' => $notificationTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'notification_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NotificationTemplate $notificationTemplate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NotificationTemplateType::class, $notificationTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('notification_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notification/notification_template/edit.html.twig', [
            'item' => $notificationTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'notification_template_delete', methods: ['POST'])]
    public function delete(Request $request, NotificationTemplate $notificationTemplate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notificationTemplate->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($notificationTemplate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('notification_template_index', [], Response::HTTP_SEE_OTHER);
    }
}
