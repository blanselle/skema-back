<?php

namespace App\Controller\Student;

use App\Entity\Student;
use App\Repository\Loggable\HistoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentHistoryController extends AbstractController
{
    #[Route('/admin/students/{id}/history', name: 'student_history', methods: ['GET'])]
    public function index(
        Student $student,
        HistoryRepository $historyRepository,
        PaginatorInterface $paginator,
        Request $request) : Response
    {
        $pagination = $paginator->paginate(
            $historyRepository->getHistories(
                student: $student,
                orderBy: [
                    $request->query->get('sort', 'h.loggedAt') => $request->query->get('direction', 'desc')
                ]),
            $request->query->getInt('page', 1),
            10,
            [
                'defaultSortFieldName' => 'h.loggedAt',
                'defaultSortDirection' => 'desc',
            ]
        );

        $pagination->setCustomParameters(['align' => 'right',]);

        return $this->render('student/history/index.html.twig', [
            'student' => $student,
            'pagination' => $pagination,
        ]);
    }
}
