<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use App\Constants\User\UserRoleConstants;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'E-mail'])
            ->add('plainPassword', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('roles', ChoiceType::class, options: [
                'choices' => [
                    'Coordinateur' => UserRoleConstants::ROLE_COORDINATOR,
                    'Responsable' => UserRoleConstants::ROLE_RESPONSABLE,
                    'Administrateur' => UserRoleConstants::ROLE_ADMIN,
                ],
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => 'user:validation'
        ]);
    }
}
