<?php

declare(strict_types=1);

namespace App\Form;

use App\Constants\Parameters\ParametersConstants;
use App\Entity\Campus;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nom',
                    'attr' => [
                        'placeholder' => 'Nom',
                    ]
                ])
            ->add('media', MediaType::class, [
                'label' => false
            ])
            ->add('addressLine1', TextType::class, [
                'label' => 'Addresse ligne 1',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Addresse ligne 1',
                ]
            ])
            ->add('addressLine2', TextType::class, [
                'label' => 'Addresse ligne 2',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Addresse ligne 2',
                ]
            ])
            ->add('addressLine3', TextType::class, [
                'label' => 'Addresse ligne 3',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Addresse ligne 3',
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Code postal',
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ville',
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'required' => false,
                'preferred_choices' => [ ParametersConstants::DEFAULT_COUNTRY ],
                'data' => ParametersConstants::DEFAULT_COUNTRY,
                'attr' => [
                    'placeholder' => 'Pays',
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email Service Concours',
                'attr' => [
                    'placeholder' => 'Email Service Concours',
                ]
            ])
            ->add('phoneReception', TextType::class, [
                'label' => 'Téléphone accueil',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Téléphone accueil',
                ]
            ])
            ->add('phoneCustomerService', TextType::class, [
                'label' => 'Téléphone Service Concours',
                'attr' => [
                    'placeholder' => 'Téléphone Service Concours',
                ]
            ])
            ->add('instruction', CKEditorType::class, options: ['label' => 'Indications spécifiques campus'])
            ->add('assignmentCampus', CheckboxType::class, ['required' => false, 'label' => 'Campus d’affectation'])
            ->add('oralTestCenter', CheckboxType::class, ['required' => false, 'label' => 'Centre d’épreuves orales'])
            ->add('contestJuryWebsiteCode', TextType::class, ['required' => false, 'label' => 'Code site jury concours'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campus::class,
        ]);
    }
}
