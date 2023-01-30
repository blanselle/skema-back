<?php

declare(strict_types=1);

namespace App\Controller\OralTest\OralStudentTest;

use App\Entity\OralTest\OralTestStudent;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/oral-test/student')]
class OralStudentTestController extends AbstractController
{
    #[Route('/{id}', name: 'oral_test_student_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        OralTestStudent $oralTestStudent,
        EntityManagerInterface $em,
        StudentWorkflowManager $studentWorkflowManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$oralTestStudent->getId(), strval($request->request->get('_token')))) {
            $studentWorkflowManager->registeredEoToCancelEo($oralTestStudent->getStudent());
            $em->remove($oralTestStudent);
            $em->flush();

            $this->addFlash('success', 'Le candidat est désinscrit de la session oral'); 
        }

        return $this->redirectToRoute('student_edit', ['id' => $oralTestStudent->getStudent()->getId()]);
    }
}