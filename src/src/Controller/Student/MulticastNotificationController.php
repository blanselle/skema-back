<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaStateSimplifyConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\User;
use App\Model\Notification\MulticastNotification;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Notification\MulticasterDispatcher;
use App\Form\Notification\MulticastNotificationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/students/notification')]
#[IsGranted('ROLE_COORDINATOR')]
class MulticastNotificationController extends AbstractController
{
    public const BULLETIN = 'Relevé de notes';

    #[Route('/', name: 'multicast_notification', methods: ['GET', 'POST'])]
    public function __invoke(
        Request $request,
        MulticasterDispatcher $dispatcher,
        TranslatorInterface $translator,
        StudentRepository $studentRepository,
    ): Response {

        $studentStates = [];
        foreach (StudentWorkflowStateConstants::getConsts() as $key => $value) {
            $studentStates[constant(StudentWorkflowStateConstants::class . '::' . $key)] = $translator->trans('workflow.student.'.strtolower($value));
        }
        asort($studentStates);

        $mediaCodes = ['bulletin' => self::BULLETIN];
        foreach (MediaCodeConstants::getConsts() as $key => $value) {
            if (str_starts_with(constant(MediaCodeConstants::class . '::' . $key), 'bulletin_')) {
                continue;
            }
            $mediaCodes[constant(MediaCodeConstants::class . '::' . $key)] = $translator->trans('media.codes.'.strtolower($value));
        }
        asort($mediaCodes);

        $mediaStates = MediaStateSimplifyConstants::MEDIA_STATES;

        asort($mediaStates);

        $multicastNotification = (new MulticastNotification())
            ->setIdentifier($request->query->get('identifier'))
            ->setLastname($request->query->get('lastname'))
            ->setState($request->query->get('state'))
            ->setMediaCode($request->query->get('mediaCode'))
            ->setMedia($request->query->get('media'))
        ;

        $form = $this->createForm(MulticastNotificationType::class, $multicastNotification, [
            'studentStates' => $studentStates,
            'mediaCodes' => $mediaCodes,
            'mediaStates' => $mediaStates,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $students = $studentRepository->getStudentsListFiltered(['filters' => [
                'identifier' => $multicastNotification->getIdentifier(),
                'lastname' => $multicastNotification->getLastname(),
                'state' => $multicastNotification->getState(),
                'media' => $multicastNotification->getMedia(),
                'mediaCode' => $multicastNotification->getMediaCode(),
                'intern' => $request->query->getBoolean('intern', true),
                'externalSession' => $request->query->getBoolean('externalSession')
            ]]);

            $dispatcher->dispatch(
                notification: $multicastNotification,
                studentIds: array_map(function($s) { return $s->getId(); }, $students),
                sender: $user,
            );

            return $this->redirectToRoute('student_index', $request->query->all(), Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notification/multicast/notification.html.twig', [
            'form' => $form,
        ]);
    }
}
