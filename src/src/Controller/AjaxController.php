<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\Media\MediaCodeConstants;
use Exception;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\Bloc\Bloc;
use App\Entity\CV\Experience;
use App\Entity\CV\Bac\BacType;
use App\Entity\CV\Bac\BacOption;
use App\Entity\CV\Bac\BacChannel;
use App\Service\CandidateManager;
use App\Entity\Admissibility\Param;
use App\Entity\Admissibility\Border;
use App\Manager\NotificationManager;
use App\Constants\Bloc\BlocConstants;
use App\Entity\Diploma\DiplomaChannel;
use App\Entity\Exam\ExamClassification;
use Doctrine\ORM\EntityManagerInterface;
use App\Manager\Admissibility\ParamManager;
use App\Repository\Diploma\DiplomaRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Constants\Exam\ExamConditionConstants;
use Symfony\Component\HttpFoundation\Response;
use App\Manager\ExamClassificationScoreManager;
use App\Service\Notification\NotificationMedia;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Notification\NotificationPopupType;
use App\Service\Notification\NotificationCenter;
use App\Entity\Notification\NotificationTemplate;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Repository\Diploma\DiplomaChannelRepository;
use App\Service\Workflow\Media\MediaWorkflowManager;
use App\Constants\Notification\NotificationConstants;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Workflow\Student\StudentWorkflowManager;
use App\Constants\CV\Experience\ExperienceStateConstants;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Constants\Notification\NotificationTemplateTagConstants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/admin/ajax')]
class AjaxController extends AbstractController
{
    public function __construct(
        private MediaWorkflowManager $mediaWorkflow,
        private EntityManagerInterface $em,
        private NotificationCenter $notificationCenter,
        private TranslatorInterface $translator,
        private NotificationManager $notificationManager,
        private StudentWorkflowManager $studentWorkflowManager,
        private ExamClassificationScoreManager $examClassificationScoreManager,
        private ParamManager $paramManager,
    ) {
    }

    #[Route('/media/preview', name: 'ajax_media_preview', methods: ['GET'])]
    public function preview(Request $request): Response
    {
        return $this->render('popup/_preview_image.html.twig', [
            'media' => $request->query->get('src')
        ]);
    }

    #[Route('/media/notification/{media}/tag/{tag}', name: 'ajax_media_notification', methods: ['GET', 'POST'])]
    public function notification(Media $media, EntityManagerInterface $em, string $tag): Response
    {
        $notificationTemplates = $em->getRepository(NotificationTemplate::class)->findBy(['tag' => $tag], ['subject' => 'asc']);

        $receivers = [];
        if ($tag == NotificationTemplateTagConstants::TAG_MEDIA_TRANSFER) {
            $receivers = $em->getRepository(User::class)->findAllExceptCandidate();
        }

        $formContent =  $this->createForm(NotificationPopupType::class);

        return $this->render('popup/_notification.html.twig', [
            'notifications' => $notificationTemplates,
            'media' => $media,
            'tag' => $tag,
            'receivers' => $receivers,
            'formContent' => $formContent->createView(),
        ]);
    }

    #[Route('/student/{id}/cancelation', name: 'ajax_student_cancelation', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_COORDINATOR')]
    public function cancelation(Student $student, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->studentWorkflowManager->cancelation($student);

            $params = [
                'motif' => $request->request->get('motif'),
                'firstname' => $student->getUser()->getFirstName(),
            ];

            $notification = $this->notificationManager->createNotification(
                receiver: $student->getUser(),
                blocKey: BlocConstants::BLOC_NOTIFICATION_RESIGNATION,
                params: $params,
                private: true,
            );

            $this->notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_EMAIL, NotificationConstants::TRANSPORT_DB], sendGenericMail: false);
        }

        return $this->render('popup/_resignation.html.twig', [
            'media' => $request->query->get('src'),
            'student' => $student
        ]);
    }

    #[Route('/media/notification/{media}/send', name: 'ajax_media_notification_send', methods: ['GET', 'POST'])]
    public function send(Media $media, Request $request, NotificationMedia $notificationMedia): Response
    {
        if ($request->isMethod('POST')) {

            $tag = $request->request->get('tag');

            if(null === $tag) {
                throw new Exception('Tag manquant');
            }

            if($tag === NotificationTemplateTagConstants::TAG_MEDIA_REJECTION || $tag === NotificationTemplateTagConstants::TAG_MEDIA_TRANSFER) {

                $subject = $request->request->get('subject');
                $content = $request->request->get('content');

                if(null === $subject) {
                    throw new Exception('Subject manquant');
                }

                if(null === $content) {
                    throw new Exception('Content manquant');
                }
                
                if($tag === NotificationTemplateTagConstants::TAG_MEDIA_REJECTION) {
                        
                    $this->mediaWorkflow->toCheckToRejected($media);
                    $this->mediaWorkflow->transferedToRejected($media);
                    $this->mediaWorkflow->acceptedToRejected($media);

                    if ($media->getCode() === MediaCodeConstants::CODE_CROUS) {
                        $this->studentWorkflowManager->createdToPay($media->getStudent());
                    }

                    $this->em->flush();
                    
                    $notification = $notificationMedia->generateRejectedNotification(
                        $media,
                        (string)$subject,
                        (string)$content,
                    );

                    $this->notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB]);
                }

                if($tag === NotificationTemplateTagConstants::TAG_MEDIA_TRANSFER) {

                    $this->mediaWorkflow->checkToTransfered($media);
                    $this->em->flush();
                    
                    $receiverId = $request->request->get('receiver');
                    
                    if(null === $receiverId) {
                        throw new Exception('Receiver non valide');
                    }
                    
                    $notification = $notificationMedia->generateTransferredNotification(
                        (string)$subject,
                        (string)$content,
                        (string)$receiverId,
                        $media,
                    );

                    $this->notificationCenter->dispatch(
                        $notification, 
                        [
                            NotificationConstants::TRANSPORT_DB,
                            NotificationConstants::TRANSPORT_EMAIL,
                        ],
                        sendGenericMail: false,
                    );
                }
            }
        }
        
        return new JsonResponse([
            'id' => $media->getId(),
            'state' => $media->getState(),
            'state_label' => $this->translator->trans($media->getState()),
        ], 200);
    }
    
    #[Route('/media/validate', name: 'ajax_media_validate', methods: ['POST'])]
    public function validateDocument(Request $request, CandidateManager $candidateManager, EntityManagerInterface $em): Response
    {
        $documentsValidated = false;
        /** @var Media $media */
        $media = $this->em->getRepository(Media::class)->findOneBy(['id' => $request->request->get('media')]);

        if (null != $media) {
            $submittedValidation = $request->request->get('choice');
            if ($submittedValidation == MediaWorflowStateConstants::STATE_ACCEPTED) {
                $this->mediaWorkflow->uploadedToCheck($media);
                $this->mediaWorkflow->checkToAccepted($media);
                $this->mediaWorkflow->transferedToAccepted($media);
                $documentsValidated = $candidateManager->hasAllDocumentsValidated($media->getStudent());

                if ($media->getCode() === MediaCodeConstants::CODE_CROUS) {
                    $this->studentWorkflowManager->valid($media->getStudent());
                    $this->studentWorkflowManager->eligible($media->getStudent());
                    $this->studentWorkflowManager->eligibleToComplete($media->getStudent());
                }

                if ($media->getCode() === MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE) {
                    $this->studentWorkflowManager->acceptCheckDiploma($media->getStudent()); 
                }

                $em->flush();
            }
        }

        return new JsonResponse(
            [
                'state' => $this->translator->trans($media->getState()),
                'documentsValidated' => $documentsValidated
            ], 200
        );
    }

    #[Route('/candidate/validate', name: 'ajax_candidate_validate', methods: ['POST'])]
    public function validateCandidate(Request $request): Response
    {
        /** @var Media $media */
        $media = $this->em->getRepository(Media::class)->findOneBy(['id' => $request->request->get('media')]);

        $this->studentWorkflowManager->approved($media->getStudent());

        return new JsonResponse('OK', 200);
    }

    #[Route('/cv/experience/{id}/validate', name: 'ajax_cv_experience_validate', methods: ['POST'])]
    public function validateCvExperience(Experience $experience, Request $request, TranslatorInterface $translator): Response
    {
        $submittedValidation = $request->request->get('choice');
        if ($submittedValidation == MediaWorflowStateConstants::STATE_REJECTED) {
            $experience->setState($translator->trans(ExperienceStateConstants::STATE_REJECTED, [], 'messages'));
            $this->em->flush();
        }

        return new JsonResponse($experience->getState(), 200);
    }

    #[Route('/diploma/channel', name: 'ajax_diploma_channel', methods: ['GET'])]
    public function getDiplomaChannelsByDiploma(Request $request): Response
    {
        $diploma = (int)$request->query->get('id');
        $diplomaChannels = $this->em->getRepository(DiplomaChannel::class)->getDiplomaChannelsByDiploma($diploma);

        return $this->render('student/_form_diploma_channels.html.twig', [
            'diplomaChannels' => $diplomaChannels
        ]);
    }

    #[Route('/admissibility/param/{id}/border', name: 'ajax_admissibility_border_post', methods: ['POST'])]
    public function saveAdmissibilityBorder(Param $param, Request $request): Response
    {
        $score = $request->request->get('score');
        $note = $request->request->get('note');

        if (
            !$this->examClassificationScoreManager->scoreExists(
                $param->getAdmissibility()->getExamClassification(),
                (float) $score
            )
        ) {
            return new JsonResponse(['error' => sprintf(
                '%d is not a valid score for exam classification %s',
                $score,
                $param->getAdmissibility()->getExamClassification()->getName()
            )], Response::HTTP_NOT_FOUND);
        }

        $border = new Border();
        $border->setScore((float)$score)
            ->setNote((float)$note)
        ;

        $param->addBorder($border);
        $this->em->persist($border);

        if (!$this->paramManager->checkBordersConsistency($param)) {
            return new JsonResponse(['error' => 'Borders are not consistent'], Response::HTTP_FORBIDDEN);
        }

        $this->em->flush();

        return new JsonResponse($param, 200);
    }

    #[Route('/admissibility/border/{id}', name: 'ajax_admissibility_border_delete', methods: ['POST'])]
    public function deleteAdmissibilityBorder(Border $border): Response
    {
        $this->em->remove($border);
        $this->em->flush();

        return new JsonResponse('OK', 200);
    }

    #[Route('/diploma/need-detail', name: 'ajax_diploma_need_detail', methods: ['GET'])]
    public function isDiplomaNeedDetail(
        DiplomaRepository $diplomaRepository,
        DiplomaChannelRepository $diplomaChannelRepository,
        Request $request): Response
    {
        $diplomaId = $request->query->getInt('diplomaId');
        $diploma = $diplomaRepository->find($diplomaId);
        if (null === $diploma) {
            throw new NotFoundHttpException(sprintf('Diploma not found with identifier %d', $diplomaId));
        }

        $diplomaChannelId = $request->query->getInt('diplomaChannelId');
        $diplomaChannel = $diplomaChannelRepository->find($diplomaChannelId);

        return new JsonResponse(['needDetail' => $diploma->getNeedDetail() || $diplomaChannel?->getNeedDetail()]);
    }

    #[Route('/bloc/{id}/media', name: 'ajax_bloc_media_delete', methods: ['POST'])]
    public function deleteBlocMedia(Bloc $bloc): Response
    {
        $bloc->setMedia(null);
        $this->em->flush();

        return new JsonResponse('OK', 200);
    }

    #[Route('/bac/type', name: 'ajax_bac_type', methods: ['GET'])]
    public function getBacTypesByBacChannel(Request $request): Response
    {
        $bacChannel = $this->em->getRepository(BacChannel::class)->findOneBy(['id' => (int)$request->query->get('id')], []);
        $year = $request->get('year', null);
        if(is_string($year)) {
            $year = intval($year);
        }

        $bacTypes = $this->em->getRepository(BacType::class)->getBacTypesByBacChannel($bacChannel, $year);

        return $this->render('student/_form_bac_types.html.twig', [
            'bacTypes' => $bacTypes
        ]);
    }

    #[Route('/bac/option', name: 'ajax_bac_option', methods: ['POST'])]
    public function getBacOptionsByBacType(Request $request): Response
    {
        $bacTypes = $request->get('ids', []);

        $bacOptions = $this->em->getRepository(BacOption::class)->getBacOptionsByBacType($bacTypes);

        return $this->render('student/_form_bac_options.html.twig', [
            'bacOptions' => $bacOptions
        ]);
    }

    #[Route('/exam/typologie/online', name: 'ajax_exam_typologie_online', methods: ['POST'])]
    public function isTypologieOnline(Request $request): Response
    {
        $return = true;
        $examClassificationId = $request->request->get('examClassification');
        $examClassification = $this->em->getRepository(ExamClassification::class)->findOneBy(['id' => $examClassificationId]);
        if ($examClassification->getExamCondition()->getName() === ExamConditionConstants::CONDITION_IN_PERSON) {
            $return = false;
        }

        return new JsonResponse($return, 200);
    }

    #[Route('/media/{media}/revalidate', name: 'ajax_media_revalidate', methods: ['POST'])]
    public function revalidate(Media $media, CandidateManager $candidateManager, EntityManagerInterface $em): Response
    {
        $this->mediaWorkflow->acceptedToCheck($media);
        $this->mediaWorkflow->checkToAccepted($media);

        $documentsValidated = $candidateManager->hasAllDocumentsValidated($media->getStudent());

        if ($media->getCode() === MediaCodeConstants::CODE_CROUS) {
            $this->studentWorkflowManager->valid($media->getStudent());
            $this->studentWorkflowManager->eligible($media->getStudent());
            $this->studentWorkflowManager->eligibleToComplete($media->getStudent());
        }

        if ($media->getCode() === MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE) {
            $this->studentWorkflowManager->acceptCheckDiploma($media->getStudent());
        }

        $em->flush();

        return new JsonResponse(
            [
                'state' => $this->translator->trans($media->getState()),
                'documentsValidated' => $documentsValidated
            ], 200
        );
    }
}
