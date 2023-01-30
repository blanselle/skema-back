<?php

declare(strict_types=1);

namespace App\Form\Admin\User\AdministrativeRecord;

use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\AdministrativeRecord\ScholarShipLevel;
use App\Entity\AdministrativeRecord\SportLevel;
use App\Entity\Exam\ExamLanguage;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdministrativeRecordType extends AbstractFunctionsType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('optionalExamLanguage', ChoiceType::class, [
                'required' => false,
                'label' => 'LV2 (optionnelle)',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'placeholder' => 'Non renseigné',
                'disabled' => $options['attr']['exemption'],
            ])
            ->add('examLanguage', EntityType::class, [
                'label' => 'LV2',
                'class' => ExamLanguage::class,
                'choices' => $this->em->getRepository(ExamLanguage::class)->findByProgramChannelIds($options['programChannel'], ['name' => 'asc']),
                'choice_label' => function ($exam) {
                    return $exam->getName();
                },
                'disabled' => $options['attr']['exemption'],
                'required'   => false,
                'empty_data' => null
            ])
            ->add('highLevelSportsman', ChoiceType::class, [
                'required' => false,
                'label' => 'Sportif de haut niveau',
                'placeholder' => 'Non renseigné',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'disabled' => $options['attr']['exemption']
            ])
            ->add('sportLevel', EntityType::class, [
                'label' => 'Niveau',
                'required' => false,
                'class' => SportLevel::class,
                'choices' => $this->em->getRepository(SportLevel::class)->findBy([], ['label' => 'asc']),
                'choice_label' => function ($sport) {
                    return $sport->getLabel();
                },
                'disabled' => $options['attr']['exemption']
            ])
            ->add('thirdTime', ChoiceType::class, [
                'label' => 'Tiers temps',
                'placeholder' => 'Non renseigné',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'required' => false,
                'disabled' => $options['attr']['exemption']
            ])
            ->add('thirdTimeNeedDetail', ChoiceType::class, [
                'label' => 'Autre aménagement',
                'placeholder' => 'Non renseigné',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'required' => false,
                'disabled' => $options['attr']['exemption']
            ])
            ->add('thirdTimeDetail', TextType::class, ['label' => 'Détail', 'required' => false, 'disabled' => $options['attr']['exemption']])
            ->add('scholarShip', ChoiceType::class, [
                'required' => false,
                'label' => 'Boursier',
                'placeholder' => 'Non renseigné',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'disabled' => $options['attr']['exemption']
            ])
            ->add('scholarShipLevel', EntityType::class, [
                'label' => 'Niveau',
                'required' => false,
                'class' => ScholarShipLevel::class,
                'choices' => $this->em->getRepository(ScholarShipLevel::class)->findBy([], ['label' => 'asc']),
                'choice_label' => function ($scholar) {
                    return $scholar->getLabel();
                },
                'disabled' => $options['attr']['exemption']
            ])
            ->add('studentDiplomas', CollectionType::class, [
                'entry_type' => StudentDiplomaType::class,
                'label' => false,
                'entry_options' => ['label' => false, 'programChannel' => $options['programChannel']],
                'disabled' => $options['attr']['exemption'],
                'allow_extra_fields' => true
            ])
            ->add('jdc', MediaType::class, [
                'label' => false,
                'disabled' => $options['attr']['exemption'],
                'required' => false,
            ])
        ;


        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($options) {

                $student = $event->getData()->getStudent();
                $form = $event->getForm();

                if (!empty($student) && !$this->isMediaValid($student->getAdministrativeRecord()->getThirdTimeMedias(), 1)) {
                    $form->add('thirdTimeMedias', CollectionType::class, [
                        'entry_type' => MediaType::class,
                        'label' => false,
                        'entry_options' => ['label' => false],
                        'disabled' => $options['attr']['exemption'],
                        'allow_add' => true,
                        'prototype' => true,
                        'by_reference' => false,
                        'required' => false,
                    ]);
                }

                if (!empty($student) && !$this->isMediaValid($student->getAdministrativeRecord()->getScholarShipMedias(), 2)) {
                    $form->add('scholarShipMedias', CollectionType::class, [
                        'entry_type' => MediaType::class,
                        'label' => false,
                        'entry_options' => ['label' => false],
                        'disabled' => $options['attr']['exemption'],
                        'allow_add' => true,
                        'prototype' => true,
                        'by_reference' => false,
                        'required' => false,
                    ]);
                }

                if (!empty($student) && !$this->isMediaValid($student->getAdministrativeRecord()->getIdCards(), 1)) {
                    $form->add('idCards', CollectionType::class, [
                        'entry_type' => MediaType::class,
                        'label' => false,
                        'entry_options' => ['label' => false],
                        'disabled' => $options['attr']['exemption'],
                        'allow_add' => true,
                        'prototype' => true,
                        'by_reference' => false,
                        'required' => false,
                    ]);
                }

                if (!empty($student) && !$this->isMediaValid($student->getAdministrativeRecord()->getHighLevelSportsmanMedias(), 1)) {
                    $form->add('highLevelSportsmanMedias', CollectionType::class, [
                        'entry_type' => MediaType::class,
                        'label' => false,
                        'entry_options' => ['label' => false],
                        'disabled' => $options['attr']['exemption'],
                        'allow_add' => true,
                        'prototype' => true,
                        'by_reference' => false,
                        'required' => false,
                    ]);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdministrativeRecord::class,
            'programChannel' => null,
            'validation_groups' => ['bo'],
        ]);
    }
}
