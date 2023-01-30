<?php

declare(strict_types=1);

namespace App\Form\Admin\User\CV;

use App\Constants\CV\BacSupConstants;
use App\Entity\Country;
use App\Entity\CV\BacSup;
use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BacSupType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $diplomas = $this->em->getRepository(Diploma::class)->getDiplomasByProgramChannel($options['programChannel']);
        $builder
            ->add('id', HiddenType::class)
            ->add('diploma', EntityType::class, [
                'label' => 'Diplôme',
                'class' => Diploma::class,
                'choices' => $diplomas,
                'choice_label' => function ($diploma) {
                    return $diploma->getName();
                },
                'row_attr' => ['class' => 'mb-3 diploma'],
                'placeholder' => 'Sélectionnez un diplôme',
                'required' => true,
            ])
            ->add('establishment', TextType::class, [
                'label' => 'Établissement',
                'required' => true,
            ])
            ->add('year', NumberType::class, [
                'label' => 'Année',
                'required' => true,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ])
            ->add('country', EntityType::class, [
                'label' => 'Pays',
                'class' => Country::class,
                'choices' => $this->em->getRepository(Country::class)->findBy(['active' => true], ['name' => 'asc']),
                'choice_label' => function ($country) {
                    return $country->getName();
                },
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Semestriel' => BacSupConstants::TYPE_SEMESTRIAL,
                    'Annuel' => BacSupConstants::TYPE_ANNUAL,
                ],
                'required' => true,
            ])
            ->add('schoolReports', CollectionType::class, [
                'entry_type' => SchoolReportType::class,
                'label' => false,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
            ])
            ->add('identifier', HiddenType::class)
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
                    'row_attr' => ['class' => 'mb-3 diploma-channel'],
                    'placeholder' => 'Sélectionnez une filière',
                ])
            ;
        };

        $formDetailModifier = function (FormInterface $form, Diploma $diploma = null, DiplomaChannel $diplomaChannel = null) {
            $form
                ->add('detail', TextType::class, [
                    'label' => 'Précision',
                    'required' => true === $diploma?->getNeedDetail() || true === $diplomaChannel?->getNeedDetail(),
                    'disabled' => !(true === $diploma?->getNeedDetail() || true === $diplomaChannel?->getNeedDetail()),
                    'row_attr' => ['class' => 'mb-3 detail']
                ])
            ;
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier, $formDetailModifier, $diplomas) {
                /** @var BacSup $bacSup */
                $bacSup = $event->getData();
                $diploma = $bacSup->getDiploma()?? $diplomas[0];

                $formModifier($event->getForm(), $diploma);
                $formDetailModifier($event->getForm(), $diploma, $bacSup->getDiplomaChannel());
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
            FormEvents::SUBMIT,
            function(FormEvent $event) use ($formDetailModifier) {
                /** @var BacSup $bacSup */
                $bacSup = $event->getForm()->getData();
                $diploma = $event->getForm()->get('diploma')->getData();
                $diplomaChannel = $event->getForm()->get('diplomaChannel')->getData();

                $event->getForm()->remove('detail');
                if (!(true === $diploma?->getNeedDetail() || true === $diplomaChannel?->getNeedDetail())) {
                    $bacSup->setDetail(null);
                }

                $formDetailModifier($event->getForm(), $diploma, $diplomaChannel);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BacSup::class,
            'validation_groups' => ['bo'],
            'programChannel' => null
        ]);
    }
}
