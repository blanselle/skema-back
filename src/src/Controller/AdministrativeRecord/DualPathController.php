<?php

namespace App\Controller\AdministrativeRecord;

use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Media;
use App\Form\Admin\User\AdministrativeRecord\StudentDiplomaType;
use App\Service\Workflow\Media\MediaWorkflowManager;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/administrative_record/dual_path')]
#[IsGranted('ROLE_COORDINATOR')]
class DualPathController extends AbstractController
{
    #[Route('/{id}/student_diploma/new',  name: 'ar_dual_path_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AdministrativeRecord $administrativeRecord,
        StudentWorkflowManager $studentWorkflowManager,
    ): Response {
        $student = $administrativeRecord->getStudent();
        $exemption = false;
        if ($studentWorkflowManager->isProfilStudentDisabled($student)) {
            $exemption = true;
        }

        $studentDiploma = new StudentDiploma();
        $studentDiploma->setYear($student->getAdministrativeRecord()->getStudentDiplomas()[0]->getYear());

        $form = $this->createForm(StudentDiplomaType::class, $studentDiploma, [
            'attr' => ['exemption' => $exemption],
            'programChannel' => $student->getProgramChannel()->getId(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $studentDiploma->setAdministrativeRecord($student->getAdministrativeRecord());
            $entityManager->persist($studentDiploma);

            $student->getAdministrativeRecord()->getStudentDiplomas()[0]->setDualPathStudentDiploma($studentDiploma);
            $entityManager->flush();

            return $this->redirectToRoute('student_edit', ['id' => $student->getId(), '_fragment' => 'ardualpath']);
        }


        return $this->renderForm('student/dual_path_student_diploma_new.html.twig', [
            'form' => $form,
            'programChannel' => $administrativeRecord->getStudent()->getProgramChannel()->getId(),
            'action' => $this->generateUrl('student_edit_administrative_record', ['id' => $administrativeRecord->getStudent()->getId()]),
            'student' => $student,
        ]);
    }

    #[Route('/{id}/remove',  name: 'ar_dual_path_remove', methods: ['GET'])]
    public function remove(
        EntityManagerInterface $entityManager,
        StudentDiploma $studentDiploma,
        MediaWorkflowManager $mediaWorkflowManager
    ):  Response {
        $administrativeRecord = $studentDiploma->getAdministrativeRecord();
        /** @var Media $media */
        foreach ($studentDiploma->getDiplomaMedias() as $media) {
            $mediaWorkflowManager->toCancel($media);
            $studentDiploma->removeDiplomaMedia($media);
        }
        $entityManager->flush();
        $administrativeRecord->removeStudentDiploma($studentDiploma);
        $administrativeRecord->getStudentDiplomas()->first()->setDualPathStudentDiploma(null);
        $entityManager->remove($studentDiploma);
        $entityManager->flush();

        return $this->redirectToRoute('student_edit', ['id' => $administrativeRecord->getStudent()->getId(), '_fragment' => 'ardualpath']);
    }
}