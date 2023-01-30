<?php

declare(strict_types=1);

namespace App\Form\Admin\User\CV;

use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Constants\CV\Experience\TimeTypeConstants;
use App\Entity\CV\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Manager\ExperienceManager;

class ExperienceType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator, private ExperienceManager $experienceManager)
    {
    }

        public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('establishment', TextType::class, ['label' => 'Établissement'])
            ->add('beginAt', DateType::class, [
                'label' => 'Début',
                'widget' => 'single_text'
            ])
            ->add('endAt', DateType::class, [
                'label' => 'Fin',
                'widget' => 'single_text'
            ])
            ->add('timeType', ChoiceType::class, [
                'label' => 'Type de mission',
                'choices' => [
                    'Temps plein' => TimeTypeConstants::FULL_TIME,
                    'Temps partiel' => TimeTypeConstants::PARTIAL_TIME,
                ],
                'attr' => [
                    'class' => 'time_type'
                ],
            ])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('experienceType', ChoiceType::class, [
                'label' => 'Type d\'expérience',
                'choices' => [
                    'Expérience professionnelle' => ExperienceTypeConstants::TYPE_PROFESSIONAL,
                    'Expérience associative' => ExperienceTypeConstants::TYPE_ASSOCIATIVE,
                    'Expérience internationale' => ExperienceTypeConstants::TYPE_INTERNATIONAL,
                ],
                'attr' => [
                    'class' => 'experience_type',
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $experience = $event->getData();
            $form = $event->getForm();

            $form
                ->add('hoursPerWeek', NumberType::class, [
                    'label' => 'Heure(s) par semaine',
                    'required' => false,
                    'attr' => [
                        'readonly' => ($experience->getExperienceType() !== ExperienceTypeConstants::TYPE_PROFESSIONAL || $experience->getTimeType() === TimeTypeConstants::FULL_TIME),
                        'class' => 'hours_per_week'
                    ]
                ])
                ->add('state', TextType::class, [
                    'label' => 'Statut',
                    'attr' => [
                        'readonly' => true
                    ],
                    'data' => $this->translator->trans($experience->getState(), [], 'messages')
                ])
                ->add('duration', NumberType::class, $this->experienceManager->getDurationLabelForExperience($experience->getExperienceType()))
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
            'validation_groups' => ['bo'],
        ]);
    }
}
