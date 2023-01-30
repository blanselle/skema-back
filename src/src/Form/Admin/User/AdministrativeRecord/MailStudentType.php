<?php

declare(strict_types=1);

namespace App\Form\Admin\User\AdministrativeRecord;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class MailStudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('email', RepeatedType::class, [
                    'type' => EmailType::class,
                    'invalid_message' => 'Les emails sont différents',
                    'options' => ['attr' => ['onpaste' => 'return false']],
                    'label' => 'Email',
                    'required' => true,
                    'first_options'  => ['label' => 'Email'],
                    'second_options' => ['label' => 'Confirmer l\'email'],
                ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
