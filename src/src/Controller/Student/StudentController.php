<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Constants\Bloc\BlocConstants;
use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Constants\CV\Experience\TimeTypeConstants;
use App\Constants\Exam\ExamSessionTypeNameConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaStateSimplifyConstants;
use App\Constants\Media\MediaTypeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\Media\MediaWorkflowTransitionConstants;
use App\Constants\Notification\NotificationConstants;
use App\Constants\OralTest\OralTestStudentWorkflowStateConstants;
use App\Constants\User\StudentConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Exam\ExamStudent;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\User;
use App\Form\Admin\User\AdministrativeRecord\AdministrativeRecordType;
use App\Form\Admin\User\AdministrativeRecord\ExamStudentType;
use App\Form\Admin\User\AdministrativeRecord\ExamType;
use App\Form\Admin\User\AdministrativeRecord\MailStudentType;
use App\Form\Admin\User\ConnexionType;
use App\Form\Admin\User\CV\CvType;
use App\Form\Admin\User\UserType;
use App\Form\OralTest\CampusOralDay\CampusDateOralDayType;
use App\Form\Student\ExportStudentListForm;
use App\Manager\BacSupManager;
use App\Manager\NotificationManager;
use App\Manager\StudentExportManager;
use App\Manager\StudentManager;
use App\Message\StudentExportListMessage;
use App\Model\Student\ExportStudentListModel;
use App\Repository\BlocRepository;
use App\Repository\Notification\NotificationRepository;
use App\Repository\OralTest\OralTestStudentRepository;
use App\Service\Cv\BacSupSchoolReportCode;
use App\Service\Datatable;
use App\Service\Media\MediaUploader;
use App\Service\Notification\NotificationCenter;
use App\Service\Workflow\Media\MediaWorkflowManager;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Repository\Exam\ExamLanguageRepository;
use App\Service\OralTest\OralTestManager;
use Exception;

#[Route('/admin/students')]
class StudentController extends AbstractController
{
    public function __construct(
        #[Autowire('%export_private_path%')]
        private string $path,
        private BacSupManager $bacSupManager,
        private EntityManagerInterface $entityManager,
        private StudentWorkflowManager $studentWorkflowManager,
        private Security $security,
        private TranslatorInterface $translator,
        private StudentManager $studentManager,
        private NotificationRepository $notificationRepository,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    #[Route('', name: 'student_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Datatable $datatable): Response
    {
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'identifier' =>
                is_string($datatable->filter('identifier')) && !empty($datatable->filter('identifier'))
                    ? html_entity_decode($datatable->filter('identifier')) : null,
            'lastname' =>
                is_string($datatable->filter('lastname')) && !empty($datatable->filter('lastname'))
                    ? html_entity_decode($datatable->filter('lastname')) : null,
            'state' =>
                is_string($datatable->filter('state')) && !empty($datatable->filter('state'))
                    ? html_entity_decode($datatable->filter('state')) : null,
        ]);

        $data['columns'] = [
            'identifier'        => ['db' => 'a.identifier', 'label' => "Identifiant"],
            'firstName'         => ['db' => 'u.firstName', 'label' => "Prénom"],
            'lastName'          => ['db' => 'u.lastName', 'label' => "Nom"],
            'state'             => ['db' => 'a.state', 'label' => "Statut fonctionnel"],
            'programChannel'    => ['db' => 'p.name', 'label' => "Voie"],
            'action'            => ['label' => "Actions"],
        ];

        $translator = $this->translator;
        $studentStates = [];
        foreach (StudentWorkflowStateConstants::getConsts() as $key => $value) {
            $studentStates[constant(StudentWorkflowStateConstants::class . '::' . $key)] = $translator->trans('workflow.student.'.strtolower($value));
        }
        asort($studentStates);

        $mediaCodes = ['bulletin' => 'Relevé de notes'];
        foreach (MediaCodeConstants::getConsts() as $key => $value) {
            if (str_starts_with(constant(MediaCodeConstants::class . '::' . $key), 'bulletin_')) {
                continue;
            }
            $mediaCodes[constant(MediaCodeConstants::class . '::' . $key)] = $translator->trans('media.codes.'.strtolower($value));
        }
        asort($mediaCodes);

        $mediaStates = MediaStateSimplifyConstants::MEDIA_STATES;

        asort($mediaStates);

        return $datatable->getDatatableResponse($request, Student::class, $data, 'student', [
            'studentStates' => $studentStates,
            'mediaCodes' => $mediaCodes,
            'mediaStates' => $mediaStates,
            'query' => $request->query->all(),
        ]);
    }

    #[Route('/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Student $student,
        OralTestStudentRepository $oralTestStudentRepository,
        ExamLanguageRepository $examLanguageRepository,
        OralTestManager $oralTestManager,
        StudentWorkflowManager $studentWorkflowManager,
    ): Response {
        if(null === $student->getCv()) {
            $cv = new Cv();
            $cv->setStudent($student);
            $this->entityManager->persist($cv);
            $this->entityManager->flush();
        }
        $exemption = $this->getExemption($student);

        $form = $this->createForm(UserType::class, $student->getUser(), ['attr' => ['exemption' => $exemption]]);
        $form->handleRequest($request);

        $formConnexion = $this->createForm(ConnexionType::class, $student->getUser(), ['attr' => ['exemption' => $exemption]]);
        $formConnexion->handleRequest($request);

        $formAR = $this->getFormAdministrativeRecord($student, $exemption);
        $formAR->handleRequest($request);

        $formCV = $this->getFormCv($student, $exemption);
        $formCV->handleRequest($request);

        $formExam = $this->createForm(ExamType::class, $student, ['attr' => ['exemption' => $exemption,]]);
        $formExam->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($request->request->has('validation_exemption')) {
                $submittedToken = $request->request->get('token');
                $submittedValidation = $request->request->get('validation_exemption');
                if ($this->isCsrfTokenValid('student' . $student->getId(), strval($submittedToken))) {
                    if (null != $submittedValidation) {
                        switch ($submittedValidation) {
                            case StudentConstants::VALUE_VALIDATE:
                                $this->studentWorkflowManager->validateExemption($student);
                                break;
                            case StudentConstants::VALUE_REJECTED:
                                $this->studentWorkflowManager->rejectedExemption($student);
                                break;
                        }
                    }
                }
            } elseif ($request->request->has('approved_candidate')) {
                $submittedToken = $request->request->get('token');
                if ($this->isCsrfTokenValid('student' . $student->getId(), strval($submittedToken))) {
                    $this->studentWorkflowManager->completeToApproved($student);
                }
            } elseif ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->entityManager->flush();
                }
                catch (\Exception $exception) {
                    $this->addFlash('error', $exception->getMessage());
                    return $this->redirectToRoute('student_edit', ['id' => $student->getId()]);
                }
            }
        }
        
        $oralTestStudent = $oralTestStudentRepository->findOneBy([
            'state' => OralTestStudentWorkflowStateConstants::VALIDATED,
            'student' => $student,
        ]);

        if(null !== $oralTestStudent) {
            $params = [
                'date' => $oralTestStudent->getCampusOralDay()->getDate()->format('Y-m-d'),
                'campus' => $oralTestStudent->getCampusOralDay()->getConfiguration()->getCampus(),
            ];
        } else {
            $params = [
                'date' => null,
                'campus' => null,
            ];
        }

        // The first language for AST1, AST2 is English
        $firstLanguage = $examLanguageRepository->findOneBy(['key' => 'ANG']);
        $secondLanguage = $student->getAdministrativeRecord()?->getExamLanguage();

        if(null === $firstLanguage) {
            throw new Exception('FirstLanguage ANG not found');
        }

        $formOralTest = $this->createForm(CampusDateOralDayType::class, $params, [
            'programChannel' => $student->getProgramChannel(),
            'firstLanguage' => $firstLanguage, 
            'secondLanguage' => $secondLanguage, 
        ]);
        $formOralTest->handleRequest($request);
        if ($formOralTest->isSubmitted() && $formOralTest->isValid()) {
            $oralTestStudent = $oralTestManager->replaceOralTestStudent(
                student: $student,
                date: $formOralTest->getData()['date'],
                campus: $formOralTest->getData()['campus'],
                firstLanguage: $firstLanguage,
                secondLanguage: $secondLanguage,
            );
            $studentWorkflowManager->toRegisteredEo($student);
            $this->addFlash('success', 'Epreuve orale ajouté');
        }

        return $this->renderForm('student/edit.html.twig', [
            'student' => $student,
            'validate' => StudentConstants::VALUE_VALIDATE,
            'rejected' => StudentConstants::VALUE_REJECTED,
            'form' => $form,
            'formConnexion' => $formConnexion,
            'formAR' => $formAR,
            'formCV' => $formCV,
            'formExam' => $formExam,
            'formOralTest' => $formOralTest,
            'oralTestStudent' => $oralTestStudent,
            'user' => $this->security->getUser(),
            'exemption' => $exemption,
            'experienceInternational' => ExperienceTypeConstants::TYPE_INTERNATIONAL,
            'experienceAssociative' => ExperienceTypeConstants::TYPE_ASSOCIATIVE,
            'experienceProfessional' => ExperienceTypeConstants::TYPE_PROFESSIONAL,
            'fullTime' => TimeTypeConstants::FULL_TIME,
            'notifications' => $this->notificationRepository->findBy(['identifier' => $student->getIdentifier(), 'private' => true, 'read' => false], ['updatedAt' => 'desc'])
        ]);
    }

    #[Route('/{id}/edit/cv', name: 'student_edit_cv', methods: ['POST'])]
    public function submitFormCv(
        Request $request,
        Student $student,
        MediaUploader $mediaUploader,
        MediaWorkflowManager $mediaWorkflowManager,
        BacSupSchoolReportCode $bacSupSchoolReportCode,
    ): Response {
        if(null === $student->getCv()) {
            $student->setCv(new Cv());
        }
        $exemption = $this->getExemption($student);

        /** @var Form $formCV */
        $formCV = $this->getFormCv($student, $exemption);

        // Experience treated
        $originalExperiences = new ArrayCollection();
        foreach ($student->getCv()?->getExperiences()?? [] as $experience) {
            $originalExperiences->add($experience);
        }

        $data = $request->request->all($formCV->getName());
        $files = $request->files->all()['cv'];
        $data = array_replace_recursive($data, $files);
        $formCV->submit($data);

        /*
         * when the form shool report is opened but no data informed or no file submitted
         * when need to set to null the media or remove the shool report
         */
        foreach($student->getCv()->getBacSups() as $k => $bacSup) {
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
        }

        if ($formCV->isValid()) {

            if(null !== $student->getCv()) {
                foreach($student->getCv()->getBacSups() as $k => $bacSup) {
                    foreach($bacSup->getSchoolReports() as $key => $schoolReport) {
                        $media = $schoolReport->getMedia();

                        $formFile = $request->files->get('cv')['bacSups'][$k]['schoolReports'][$key]['media']['formFile'];
                        if(null === $formFile) {
                            continue;
                        }
                        if (null === $media) {
                            $media = $schoolReport->setMedia(new Media())->getMedia();
                        }
                        $media->setFormFile($formFile);
                        $media->setStudent($student);

                        $media->setType(MediaTypeConstants::TYPE_DOCUMENT_TO_VALIDATE);
                        $media->setCode($bacSupSchoolReportCode->get($schoolReport));
                        $mediaUploader->upload($media);

                        $mediaWorkflowManager->uploadedToCheck($media);
                        $this->entityManager->persist($media);
                    }
                }

                /** @var Cv $cv */
                $cv = $formCV->getData();
                foreach ($originalExperiences as $originalExperience) {
                    if (false === $cv->getExperiences()->contains($originalExperience)) {
                        $cv->removeExperience($originalExperience);
                        $this->entityManager->remove($originalExperience);
                    }
                }
            }

            $this->entityManager->flush();
            $formCV->clearErrors(true);

            if (in_array('application/json', $request->getAcceptableContentTypes(), true)) {
                return new JsonResponse();
            }
        }

        return $this->renderForm('student/cv/_form_cv.html.twig', [
            'formCV' => $formCV,
            'student' => $student,
            'exemption' => $exemption,
            'experienceInternational' => ExperienceTypeConstants::TYPE_INTERNATIONAL,
            'experienceAssociative' => ExperienceTypeConstants::TYPE_ASSOCIATIVE,
            'experienceProfessional' => ExperienceTypeConstants::TYPE_PROFESSIONAL,
            'fullTime' => TimeTypeConstants::FULL_TIME
        ]);
    }

    #[Route('/{id}/edit/administrativeRecord', name: 'student_edit_administrative_record', methods: ['POST'])]
    public function submitFormAdministrativeRecord(
        Request $request,
        Student $student,
    ): Response {
        $exemption = $this->getExemption($student);

        /** @var Form $formAR */
        $formAR = $this->getFormAdministrativeRecord($student, $exemption);
        $data = $request->request->all($formAR->getName());

        $formAR->submit($data);

        if ($formAR->isValid()) {
            $this->entityManager->flush();
            $formAR->clearErrors(true);

            if (in_array('application/json', $request->getAcceptableContentTypes(), true)) {
                return new JsonResponse();
            }
        }

        return $this->renderForm('student/_form_administrative_record.html.twig', [
            'formAR' => $formAR,
            'student' => $student,
            'exemption' => $exemption,
        ]);
    }

    #[Route('/{user}/exam/mailedit', name: 'exam_student_mail_edit', methods: ['GET', 'POST'])]
    public function editMailStudent(
        Request $request, User $user,
        EntityManagerInterface $entityManager,
        NotificationManager $notificationManager,
        NotificationCenter $notificationCenter,
        BlocRepository $blocRepository,
    ): Response
    {
        $form = $this->createForm(MailStudentType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'adresse mail est bien modifiée');

            // TODO: CREER UN BLOC
            $params = [
                'subject' => 'Mise à jour de votre adresse e-mail',
                'content' => 'Bonjour '.$user->getFirstName().', Suite à votre demande, votre adresse email a été mise à jour.',
            ];
            $notification = $notificationManager->createNotification(
                receiver: $user,
                params: $params,
            );
            $notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB]);

            return $this->redirectToRoute('student_edit', ['id' => $user->getStudent()->getId()]);
        }

        return $this->renderForm('user/edit_mail.html.twig', [
            'form' => $form,
            'user' => $user,
            'formErrors' => $form->getErrors()
        ]);
    }

    #[Route('/{student}/exam/new', name: 'exam_student_new', methods: ['GET', 'POST'])]
    public function newExamStudent(
        Request $request, Student $student,
        EntityManagerInterface $entityManager,
        MediaUploader $mediaUploader
    ): Response
    {
        $examStudent = new ExamStudent();
        $examStudent->setStudent($student);
        $form = $this->createForm(ExamStudentType::class, $examStudent, ['attr' => ['action' => 'new']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $examStudent->getMedia()?? new Media();
            $file = $request->files->get('exam_student')['media']['formFile'];
            if (!empty($file)) {
                $media->setFormFile($file);
                $media->setTransition(MediaWorkflowTransitionConstants::CHECK_TO_ACCEPTED);
                $media->setState(MediaWorflowStateConstants::STATE_ACCEPTED);
                if ($examStudent->getExamSession()->getExamClassification()->getExamSessionType() == ExamSessionTypeNameConstants::TYPE_ENGLISH) {
                    $media->setCode(MediaCodeConstants::CODE_ATTESTATION_ANGLAIS);
                }
                if ($examStudent->getExamSession()->getExamClassification()->getExamSessionType() == ExamSessionTypeNameConstants::TYPE_MANAGEMENT) {
                    $media->setCode(MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT);
                }
                $mediaUploader->upload($media);
                $entityManager->persist($media);
            }
            if (null === $examStudent->getMedia()?->getFile()) {
                $media = null;
            }
            $examStudent->setMedia($media);

            /** @todo perform payment */

            $entityManager->persist($examStudent);
            $entityManager->flush();

            $this->addFlash('success', 'L\'étudiant est bien ajouté à la session');

            return $this->redirectToRoute('student_edit', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/examStudent/edit.html.twig', [
            'form' => $form,
            'student' => $student,
            'formErrors' => $form->getErrors(true)
        ]);
    }

    #[Route('/{student}/exam/{examStudent}/edit', name: 'exam_student_edit', methods: ['GET', 'POST'])]
    public function editExamStudent(
        Request $request,
        Student $student,
        ExamStudent $examStudent,
        EntityManagerInterface $entityManager,
        StudentWorkflowManager $studentWorkflowManager,
        MediaUploader $mediaUploader,
        NotificationManager $notificationManager,
        NotificationCenter $notificationCenter
    ): Response {
        $exemption = false;
        if ($studentWorkflowManager->isProfilStudentDisabled($student)) {
            $exemption = true;
        }

        $form = $this->createForm(ExamStudentType::class, $examStudent, ['attr' => ['action' => 'edit', 'exemption' => $exemption]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (1 === $examStudent->isConfirmed()) {
                $entityManager->remove($examStudent);
                $entityManager->flush();

                $params = [
                    'nom_typologie' => $examStudent->getExamSession()->getExamClassification()->getName(),
                    'firstname' => $student->getUser()->getFirstName(),
                    'date_start' => $examStudent->getExamSession()->getDateStart()->format('d/m/Y'),
                ];

                $notification = $notificationManager->createNotification(
                    receiver: $examStudent->getStudent()->getUser(),
                    blocKey: BlocConstants::BLOC_NOTIFICATION_EXAM_SESSION_DELETE,
                    params: $params
                );
                $notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB]);

                $this->addFlash('success', 'Votre inscription à la session a bien été annulée.');

                return $this->redirectToRoute('student_edit', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
            }

            $media = $examStudent->getMedia() ?? new Media();
            $file = $request->files->get('exam_student')['media']['formFile'];
            if (!empty($file)) {
                $media->setFormFile($file);
                $media->setTransition(MediaWorkflowTransitionConstants::CHECK_TO_ACCEPTED);
                $media->setState(MediaWorflowStateConstants::STATE_ACCEPTED);
                if ($examStudent->getExamSession()->getExamClassification()->getExamSessionType() == ExamSessionTypeNameConstants::TYPE_ENGLISH) {
                    $media->setCode(MediaCodeConstants::CODE_ATTESTATION_ANGLAIS);
                }
                if ($examStudent->getExamSession()->getExamClassification()->getExamSessionType() == ExamSessionTypeNameConstants::TYPE_MANAGEMENT) {
                    $media->setCode(MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT);
                }
                $mediaUploader->upload($media);
                $entityManager->persist($media);
            }
            if (null === $examStudent->getMedia()?->getFile()) {
                $media = null;
            }

            /** @todo perform payment */

            $examStudent->setMedia($media);
            $entityManager->flush();

            $flashMessage = $examStudent->isConfirmed() === 2 ? 'Votre inscription à la session a bien été enregistrée.' : null;
            if (!is_null($flashMessage)) {
                $this->addFlash('success', $flashMessage);
            }

            return $this->redirectToRoute('student_edit', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/examStudent/edit.html.twig', [
            'form' => $form,
            'formErrors' => $form->getErrors(),
            'student' => $student,
            'examStudent' => $examStudent,
            'exemption' => $exemption
        ]);
    }

    #[Route('/{student}/exam/{examStudent}', name: 'exam_student_delete', methods: ['POST'])]
    public function deleteExamStudent(Request $request, Student $student, ExamStudent $examStudent, EntityManagerInterface $entityManager, NotificationManager $notificationManager, NotificationCenter $notificationCenter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$examStudent->getId(), strval($request->request->get('_token')))) {
            $typology = $examStudent->getExamSession()->getExamClassification()->getName();
            $examSessionDateStart = $examStudent->getExamSession()->getDateStart()->format('d/m/Y');
            $media = $examStudent->getMedia();
            if (null !== $media) {
                $media->setState('cancelled');
            }
            $params = [
                'nom_typologie' => $typology,
                'firstname' => $student->getUser()->getFirstName(),
                'date_start' => $examSessionDateStart,
            ];
            $notification = $notificationManager->createNotification(
                receiver: $examStudent->getStudent()->getUser(),
                blocKey: $examStudent->getExamSession()->getType() === ExamSessionTypeConstants::TYPE_OUTSIDE ?
                              BlocConstants::BLOC_NOTIFICATION_EXAM_SESSION_RESIGNATION : BlocConstants::BLOC_NOTIFICATION_EXAM_SESSION_DELETE,
                params: $params
            );
            $notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB]);

            $entityManager->remove($examStudent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_edit', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export-list', name: 'admin_student_export_list', methods: ['GET', 'POST'])]
    public function exportList(Request $request, MessageBusInterface $bus): Response
    {
        $model = (new ExportStudentListModel())->setColumns(StudentExportManager::getChoices())
            ->setIdentifier($request->query->get('identifier'))
            ->setLastname($request->query->get('lastname'))
            ->setMedia($request->query->get('media'))
            ->setMediaCode($request->query->get('mediaCode'))
            ->setState($request->query->get('state'))
            ->setIntern($request->query->getBoolean('intern'))
        ;
        $form = $this->createForm(ExportStudentListForm::class, $model, [
            'action' => $this->generateUrl('admin_student_export_list', [
                'identifier' => $request->query->get('identifier'),
                'lastname' => $request->query->get('lastname'),
                'media' => $request->query->get('media'),
                'mediaCode' => $request->query->get('mediaCode'),
                'state' => $request->query->get('state'),
                'intern' => $request->query->getBoolean('intern'),
            ]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $columns = empty($model->getColumns())? StudentExportManager::getChoices() : $form->getData()->getColumns();
            $model->setColumns($columns);

            /** @var User $user */
            $user = $this->security->getUser();

            $bus->dispatch(new StudentExportListMessage(userId: $user->getId(), model: $model));

            $this->addFlash(type: 'info', message: 'Votre fichier est en cours de création. Vous recevrez une notification lorsque celui-ci sera prêt.');

            return $this->redirectToRoute('student_index', $request->query->all());
        }

        return $this->renderForm('student/_form_export_student_list.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/export-list-file/{filename<.+>}', name: 'admin_student_export_list_file', methods: ['GET'])]
    public function getExportedFile(string $filename): Response
    {
        $file = new File(sprintf('%s/%s', $this->path, $filename));

        return $this->file($file, "{$filename}");
    }

    #[Route('/student/{id}/anonymize', name: 'student_anonymization')]
    #[IsGranted('ROLE_ADMIN')]
    public function anonymize(Student $student): Response
    {
        $this->studentWorkflowManager->cancelation($student);
        $this->studentManager->anonymize($student);

        $this->addFlash('info', sprintf('L\'étudiant %s a été anonymisé', $student->getIdentifier()));

        return $this->redirectToRoute('student_index');
    }

    private function getExemption(Student $student): bool
    {
        $exemption = false;
        if ($this->studentWorkflowManager->isProfilStudentDisabled($student)) {
            $exemption = true;
        }

        return $exemption;
    }

    private function getFormCv(Student $student, bool $exemption): FormInterface
    {
        $cv = $student->getCv() ?? new Cv();
        $bacSups = $this->entityManager->getRepository(BacSup::class)->findBy(['cv' => $cv], ['year' => 'asc', 'dualPathBacSup' => 'asc']);
        $this->bacSupManager->initIdentifiersByBacSups($bacSups);
        $bacSups = new ArrayCollection($bacSups);
        $cv->setBacSups($bacSups);

        return $this->createForm(CvType::class, $cv, [
            'attr' => ['exemption' => $exemption],
            'programChannel' => $student->getProgramChannel()->getId(),
            'action' => $this->generateUrl('student_edit_cv', ['id' => $student->getId()]),
        ]);
    }

    private function getFormAdministrativeRecord(Student $student, bool $exemption): FormInterface
    {
        $newStudentDiploma = new StudentDiploma();
        $studentAdministrativeRecord = $student->getAdministrativeRecord() ?? (new AdministrativeRecord())->addStudentDiploma($newStudentDiploma)->setStudentLastDiploma($newStudentDiploma);
        return $this->createForm(AdministrativeRecordType::class, $studentAdministrativeRecord, [
            'attr' => ['exemption' => $exemption],
            'programChannel' => $student->getProgramChannel()->getId(),
            'action' => $this->generateUrl('student_edit_administrative_record', ['id' => $student->getId()]),
        ]);
    }
}
