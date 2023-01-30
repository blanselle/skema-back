<?php

declare(strict_types=1);

namespace App\Controller\Cv;

use App\Constants\Media\MediaTypeConstants;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\SchoolReport;
use App\Form\Admin\User\CV\BacSupType;
use App\Manager\BacSupManager;
use App\Service\Cv\BacSupSchoolReportCode;
use App\Service\Media\MediaUploader;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Symfony\Component\Form\Form;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/cv')]
#[IsGranted('ROLE_COORDINATOR')]
class BacSupController extends AbstractController
{
    #[Route('/{id}/bacSup/new', name: 'cv_bac_sup_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        Cv $cv, 
        MediaUploader $mediaUploader, 
        MediaWorkflowManager $mediaWorkflowManager,
        BacSupSchoolReportCode $bacSupSchoolReportCode,
        BacSupManager $bacSupManager
    ): Response {
        $bacSup = new BacSup();
        $bacSup->setIdentifier($bacSupManager->getIdentifier(cv: $cv));
        $cv->addBacSup($bacSup);
        /** @var Form $form $form */
        $form = $this->createForm(BacSupType::class, $bacSup, options: [
            'programChannel' => $cv->getStudent()->getProgramChannel()->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /*
             * when the form shool report is opened but no data informed or no file submitted
             * when need to set to null the media or remove the shool report
             */

            /**
             * @var int $key
             * @var  SchoolReport $schoolReport
             */
            foreach($bacSup->getSchoolReports() as $key => $schoolReport) {
                $file = $request->files->get('bac_sup')['schoolReports'][$key]['media']['formFile']?? null;
                $media = $schoolReport->getMedia();

                if (null === $media->getId() and null === $file) {
                    $schoolReport->setMedia(null);
                }
                if (
                    null === $schoolReport->getMedia() and
                    null === $schoolReport->getScore() and
                    null === $schoolReport->getScoreRetained()
                ) {
                    $bacSup->removeSchoolReport($schoolReport);
                }
            }

            if ($form->isValid()) {
                foreach($bacSup->getSchoolReports() as $key => $schoolReport) {
                    $file = $request->files->get('bac_sup')['schoolReports'][$key]['media']['formFile']?? null;

                    if (null === $file) {
                        continue;
                    }

                    $media = $schoolReport->getMedia();
                    $media->setFormFile($file);
                    $media->setType(MediaTypeConstants::TYPE_DOCUMENT_TO_VALIDATE);
                    $media->setCode($bacSupSchoolReportCode->get($schoolReport));
                    $media->setStudent($cv->getStudent());
                    $mediaUploader->upload($media);

                    $mediaWorkflowManager->uploadedToCheck($media);
                }

                $entityManager->persist($bacSup);

                $entityManager->flush();

                return $this->redirectToRoute('student_edit', ['id' => $cv->getStudent()->getId()], Response::HTTP_SEE_OTHER);
            }
        }
        
        return $this->renderForm('student/cv/bac_sup_new.html.twig', [
            'bacSup' => $bacSup,
            'cv' => $cv,
            'form' => $form,
        ]);
    }
}
