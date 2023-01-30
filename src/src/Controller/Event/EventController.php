<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\Datatable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/events')]
#[IsGranted('ROLE_RESPONSABLE')]
class EventController extends AbstractController
{
    #[Route('/', name: 'event_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EventRepository $eventRepository, Datatable $datatable): Response
    {
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'label'     => is_string($datatable->filter('label')) && !empty($datatable->filter('label'))
                ? html_entity_decode($datatable->filter('label')) : null,
        ]);

        $data['columns'] = [
            'label'    => ['db' => 'a.label', 'label' => "Label"],
            'action'    => ['label' => "Actions"],
        ];

        return $datatable->getDatatableResponse($request, Event::class, $data, 'event');
    }

    #[Route('/new', name: 'event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'item' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
    }
}
