<?php

declare(strict_types=1);

namespace App\Form\Admin\User\AdministrativeRecord;

use App\Constants\Exam\ExamConditionConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Exam\ExamRoom;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamStudentType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $examRooms = null;
        if (isset($options['data']) && null !== $options['data']->getExamSession()) {
            $examRooms = $options['data']->getExamSession()->getExamRooms();
        }

        $exemption = (isset($options['attr']['exemption']) && (bool)$options['attr']['exemption']);

        if (isset($options['attr']['action']) && $options['attr']['action'] == 'edit') {
            $builder
                ->add('examSession', ExamSessionType::class, ['label' => false, 'disabled' => 'disabled']);
        } elseif (isset($options['attr']['action']) && $options['attr']['action'] == 'new') {
            $builder
                ->add('examSession', EntityType::class, [
                    'label' => 'Session',
                    'class' => ExamSession::class,
                    'choices' => $this->em->getRepository(ExamSession::class)->findBy(['type' => ExamSessionTypeConstants::TYPE_INSIDE]),
                    'choice_label' => function ($exam) {
                        /**
                         * @var ExamSession $exam
                         */
                        return sprintf(
                            "%s - %s - %s",
                            $exam->getExamClassification()->getName(),
                            (!empty($exam->getCampus())) ? $exam->getCampus()->getName() : ExamConditionConstants::CONDITION_ONLINE,
                            $exam->getDateStart()->format('d/m/Y h:i')
                        );
                    },
                    'empty_data' => null,
                    'required' => false,
                ]);
        }
        $builder
            ->add(
                'score',
                NumberType::class,
                [
                    'label' => 'Score',
                    'required' => false,
                    'disabled' => $exemption,
                ]
            )
            ->add('media', MediaType::class, ['label' => false])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($examRooms, $exemption, $options) {
                $form = $event->getForm();
                /** @var ExamStudent $examStudent */
                $examStudent = $event->getData();

                if ($examStudent->getExamSession()?->getType() === ExamSessionTypeConstants::TYPE_INSIDE) {
                    $form
                        ->add('examRoom', EntityType::class, [
                            'label' => 'Salle',
                            'required' => false,
                            'class' => ExamRoom::class,
                            'choices' => (null !== $examRooms) ? $examRooms : $this->em->getRepository(ExamRoom::class)->findBy([], ['name' => 'asc']),
                            'choice_label' => function ($examRoom) {
                                return $examRoom->getName();
                            },
                            'disabled' => $exemption,
                        ])
                        ->add('absent', ChoiceType::class, [
                            'label' => 'Absent',
                            'required' => false,
                            'choices' => [
                                'Oui' => true,
                                'Non' => false,
                            ],
                            'disabled' => $exemption,
                        ]);
                    ;

                    if ((isset($options['data']) && null === $options['data']->getExamSession()?->getPrice()) &&
                        (isset($options['attr']['action'])  && $options['attr']['action'] == 'edit')) {
                        $form->add('confirmed', ChoiceType::class, [
                            'label' => 'Confirmer l\'inscription',
                            'required' => true,
                            'choices' => [
                                '' => 0,
                                'Oui' => 2,
                                'Non' => 1,
                            ],
                            'disabled' => $exemption,
                        ]);
                    }
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamStudent::class,
            'validation_groups' => ['bo'],
        ]);
    }
}
