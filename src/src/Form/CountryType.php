<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idCountry', options: [
                'label' => 'code',
            ])
            ->add('name', options: [
                'label' => 'Nom',
            ])
            ->add('nationality', options: [
                'label' => 'Nationalité',
            ])
            ->add('codeSISE', options: [
                'label' => 'Code SISE',
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'Actif',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
