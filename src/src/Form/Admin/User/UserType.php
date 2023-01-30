<?php

declare(strict_types=1);

namespace App\Form\Admin\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom', 'disabled' => $options['attr']['exemption']])
            ->add('lastName', TextType::class, ['label' => 'Nom de famille', 'disabled' => $options['attr']['exemption']])
            ->add('student', StudentType::class, ['disabled' => $options['attr']['exemption']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
