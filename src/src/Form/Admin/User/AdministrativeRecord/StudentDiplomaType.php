<?php

declare(strict_types=1);

namespace App\Form\Admin\User\AdministrativeRecord;

use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Entity\Diploma\StudentDiploma;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentDiplomaType extends AbstractFunctionsType
{
    public function __construct(private EntityManagerInterface $em) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('year', NumberType::class, ['label' => 'Année de diplomation', 'required' => false])
            ->add('diploma', EntityType::class, [
                'label' => 'Diplôme',
                'class' => Diploma::class,
                'choices' => $this->em->getRepository(Diploma::class)->getDiplomasByProgramChannel($options['programChannel']),
                'choice_label' => function ($diploma) {
                    return $diploma->getName();
                },
                'row_attr' => ['class' => 'mb-3 diploma']
            ])
            ->add('establishment', TextType::class, ['label' => 'École / Établissement'])
            ->add('postalCode', TextType::class, ['label' => 'Code postal'])
            ->add('city', TextType::class, ['label' => 'Ville'])
        ;

        $formModifier = function (FormInterface $form, Diploma $diploma = null) {
            $choices = null === $diploma? [] : $diploma->getDiplomaChannels()->toArray();
            usort($choices, function($a, $b) {
                if ($a->getName() === $b->getName()) {
                    return 0;
                }

                return $a->getName() > $b->getName()? 1 : -1;
            });

            $form
                ->add('diplomaChannel', EntityType::class, [
                    'label' => 'Filière',
                    'class' => DiplomaChannel::class,
                    'choices' => $choices,
                    'choice_label' => function ($diploma) {
                        return $diploma->getName();
                    },
                    'row_attr' => ['class' => 'mb-3 diploma-channel']
                ])
            ;
        };

        $formDetailModifier = function (FormInterface $form, Diploma $diploma = null, DiplomaChannel $diplomaChannel = null) {
            $form
                ->add('detail', TextType::class, [
                    'label' => 'Précision',
                    'required' => $diploma?->getNeedDetail() || $diplomaChannel?->getNeedDetail(),
                    'disabled' => !($diploma?->getNeedDetail() || $diplomaChannel?->getNeedDetail()),
                    'row_attr' => ['class' => 'mb-3 detail']
                ])
            ;
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier, $formDetailModifier) {
                $form = $event->getForm();

                /** @var StudentDiploma $studentDiploma */
                $studentDiploma = $event->getData();

                if (null != $studentDiploma) {
                    $formModifier($event->getForm(), $studentDiploma->getDiploma());
                    $formDetailModifier($event->getForm(), $studentDiploma->getDiploma(), $studentDiploma->getDiplomaChannel());

                    if (!$this->isMediaValid($studentDiploma->getDiplomaMedias(), 1)) {
                        $form->add('diplomaMedias', CollectionType::class, [
                            'entry_type' => MediaType::class,
                            'label' => false,
                            'entry_options' => ['label' => false],
                            'allow_add' => true,
                            'prototype' => true,
                        ]);
                    }
                }
            },
            10
        );

        $builder->get('diploma')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm()->getParent(), $event->getForm()->getData());
            },
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) use ($formDetailModifier) {
                /** @var StudentDiploma $studentDiploma */
                $studentDiploma = $event->getForm()->getData();

                $event->getForm()->remove('detail');
                if (!empty($studentDiploma->getDiploma()) && !($studentDiploma->getDiploma()->getNeedDetail() || $studentDiploma->getDiplomaChannel()?->getNeedDetail())) {
                    $studentDiploma->setDetail(null);
                }

                $formDetailModifier($event->getForm(), $studentDiploma->getDiploma(), $studentDiploma->getDiplomaChannel());
            }
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StudentDiploma::class,
            'programChannel' => null,
            'validation_groups' => ['bo'],
        ]);
    }
}
