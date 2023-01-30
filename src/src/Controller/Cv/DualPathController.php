<?php

namespace App\Controller\Cv;

use App\Entity\CV\BacSup;
use App\Entity\CV\SchoolReport;
use App\Form\Admin\User\CV\BacSupType;
use App\Service\Workflow\Media\MediaWorkflowManager;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cv/dual_path')]
#[IsGranted('ROLE_COORDINATOR')]
class DualPathController extends AbstractController
{
    #[Route('/{id}/student_diploma/new',  name: 'cv_dual_path_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        BacSup $bacSup,
        StudentWorkflowManager $studentWorkflowManager,
    ): Response {
        $cv = $bacSup->getCv();
        $student = $cv->getStudent();
        $exemption = false;
        if ($studentWorkflowManager->isProfilStudentDisabled($student)) {
            $exemption = true;
        }

        $newBacSup = new BacSup();
        $newBacSup->setYear($bacSup->getYear());
        $newBacSup->setParent($bacSup);
        $newBacSup->setCv($cv);

        $form = $this->createForm(BacSupType::class, $newBacSup, [
            'attr' => ['exemption' => $exemption],
            'programChannel' => $student->getProgramChannel()->getId(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $newBacSup->setCv($cv);
            $entityManager->persist($newBacSup);
            $entityManager->flush();

            return $this->redirectToRoute('student_edit', ['id' => $student->getId(), '_fragment' => 'cvdualpath']);
        }


        return $this->renderForm('student/cv/bac_sup_dual_path_new.html.twig', [
            'form' => $form,
            'student' => $student,
            'bacSup' => $bacSup,
            'cv' => $cv,
        ]);
    }

    #[Route('/{id}/student_diploma/remove',  name: 'cv_dual_path_remove', methods: ['GET'])]
    public function remove(
        EntityManagerInterface $entityManager,
        BacSup $bacSup,
        MediaWorkflowManager $mediaWorkflowManager
    ):  Response {
        $student = $bacSup->getCv()->getStudent();
        /** @var SchoolReport $schoolReport */
        foreach ($bacSup->getSchoolReports() as $schoolReport) {
            if (null != $schoolReport->getMedia()) {
                $mediaWorkflowManager->toCancel($schoolReport->getMedia());
            }
        }
        $bacSup->getParent()->setDualPathBacSup(null);
        $entityManager->flush();
        $entityManager->remove($bacSup);
        $entityManager->flush();

        return $this->redirectToRoute('student_edit', ['id' => $student->getId(), '_fragment' => 'cvdualpath']);
    }
}