<?php

namespace App\Controller;

use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\CV\BacSup;
use App\Entity\CV\SchoolReport;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Media;
use App\Entity\Student;
use App\Manager\BacSupManager;
use App\Service\Media\MediaUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ajax/media')]
class AjaxMediaController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MediaUploader $mediaUploader,
        private BacSupManager $bacSupManager
    )
    {}

    #[Route('/upload/{student}', name: 'ajax_media_upload', methods: ['POST'])]
    public function uploadMedia(Request $request, Student $student): Response
    {
        $index = 0;
        $field = (string)$request->request->get('field');
        if (str_starts_with($field, 'diplomaMedias_')) {
            $index = ((int)substr($field, (int)strrpos($field, '_') +1));
            $field = 'diplomaMedias_';
        }
        if (str_starts_with($field, 'report_')) {
            $index = ((int)substr($field, (int)strrpos($field, '_') +1));
            $field = 'report_';
        }
        $media = new Media();
        $media->setFormFile($request->files->get('file'));
        $this->mediaUploader->upload($media);
        
        switch ($field) {
            case 'diplomaMedias_':
                $i = 0;
                /** @var StudentDiploma $studentDiploma */
                foreach ($student->getAdministrativeRecord()->getStudentDiplomas() as $studentDiploma) {
                    if ($i == $index) {
                        if (null != $studentDiploma->getDualPathStudentDiploma()) {
                            $code = MediaCodeConstants::CODE_CERTIFICAT_DOUBLE_PARCOURS;
                        } else {
                            $code = MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE;
                        }

                        // force to cancelled all media not accepted
                        $diplomaMedias = $studentDiploma->getDiplomaMedias()->filter(function($m) use ($code) {
                            return $m->getCode() === $code and $m->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED;
                        });
                        foreach ($diplomaMedias as $diplomaMedia) {
                            $studentDiploma->removeDiplomaMedia($diplomaMedia);
                        }
                        $this->mediaUploader->forceStateMedia($media, $code, $student);
                        $studentDiploma->addDiplomaMedia($media);
                        break;
                    }
                    $i++;
                }
                break;
            case 'highLevelSportsmanMedias':
                // force to cancelled all media not accepted
                $highLevelSportsmanMedias = $student->getAdministrativeRecord()->getHighLevelSportsmanMedias()->filter(function($m) {
                    return MediaWorflowStateConstants::STATE_ACCEPTED !== $m->getState();
                });
                foreach ($highLevelSportsmanMedias as $highLevelSportsmanMedia) {
                    $student->getAdministrativeRecord()->removeHighLevelSportsmanMedia($highLevelSportsmanMedia);
                }
                $this->mediaUploader->forceStateMedia($media, MediaCodeConstants::CODE_SHN, $student);
                $student->getAdministrativeRecord()->setHighLevelSportsman(true);
                $student->getAdministrativeRecord()->addHighLevelSportsmanMedia($media);
                break;
            case 'scholarShipMedias':
                // force to cancelled all media not accepted
                $scholarShipMedias = $student->getAdministrativeRecord()->getScholarShipMedias()->filter(function($m) {
                   return MediaWorflowStateConstants::STATE_ACCEPTED !== $m->getState();
                });
                foreach ($scholarShipMedias as $scholarShipMedia) {
                    $student->getAdministrativeRecord()->removeScholarShipMedia($scholarShipMedia);
                }
                $this->mediaUploader->forceStateMedia($media, MediaCodeConstants::CODE_CROUS, $student);
                $student->getAdministrativeRecord()->setScholarShip(true);
                $student->getAdministrativeRecord()->addScholarShipMedia($media);
                break;
            case 'thirdTimeMedias':
                // force to cancelled all media not accepted
                $thirdTimeMedias = $student->getAdministrativeRecord()->getThirdTimeMedias()->filter(function($m) {
                    return MediaWorflowStateConstants::STATE_ACCEPTED !== $m->getState();
                });
                foreach ($thirdTimeMedias as $thirdTimeMedia) {
                    $student->getAdministrativeRecord()->removeThirdTimeMedia($thirdTimeMedia);
                }
                $this->mediaUploader->forceStateMedia($media, MediaCodeConstants::CODE_TT, $student);
                $student->getAdministrativeRecord()->setThirdTime(true);
                $student->getAdministrativeRecord()->addThirdTimeMedia($media);
                break;
            case 'idCards':
                $this->mediaUploader->forceStateMedia($media, MediaCodeConstants::CODE_ID_CARD, $student);
                foreach ($student->getAdministrativeRecord()->getIdCards() as $idCard) {
                    $student->getAdministrativeRecord()->removeIdCard($idCard);
                }
                $student->getAdministrativeRecord()->addIdCard($media);
                break;
            case 'jdc':
                // force to cancelled the previous media
                $oldJdc = $student->getAdministrativeRecord()->getJdc();
                if (null !== $oldJdc) {
                    $oldJdc->setState(MediaWorflowStateConstants::STATE_CANCELLED);
                    $student->getAdministrativeRecord()->setJdc(null);
                }
                $this->mediaUploader->forceStateMedia($media, MediaCodeConstants::CODE_JOURNEE_DEFENSE_CITOYENNE, $student);
                $student->getAdministrativeRecord()->setJdc($media);
                break;
            case 'bac':
                if (null !== $student->getCv()->getBac()->getMedia()) {
                    $oldMedia = $student->getCv()->getBac()->getMedia();
                    $oldMedia->setState(MediaWorflowStateConstants::STATE_CANCELLED);
                    $student->getCv()->getBac()->setMedia(null);
                }
                $this->mediaUploader->upload($media);
                $this->mediaUploader->forceStateMedia($media, MediaCodeConstants::CODE_BAC, $student);
                $student->getCv()->getBac()->setMedia($media);
                break;
            case 'report_':
                $i = 0;
                /** @var BacSup $bacSup */
                foreach ($student->getCv()->getBacSups() as $bacSup) {
                    if ($i == $index) {
                        /** @var SchoolReport $schoolReport */
                        foreach ($bacSup->getSchoolReports() as $schoolReport) {
                            if (!empty($schoolReport->getMedia())) {
                                $oldMedia = $schoolReport->getMedia();
                                $oldMedia->setState(MediaWorflowStateConstants::STATE_CANCELLED);
                                $schoolReport->setMedia(null);
                            }
                            $code = $this->bacSupManager->getSchoolReportMediaCode($student->getCv());
                            $this->mediaUploader->upload($media);
                            $this->mediaUploader->forceStateMedia($media, $code, $student);
                            $schoolReport->setMedia($media);
                        }
                        break;
                    }
                    $i++;
                }
                break;
        }
        $this->em->persist($media);
        $this->em->flush();

        return $this->render('_media.html.twig', ['medias' => [$media]]);
    }
}