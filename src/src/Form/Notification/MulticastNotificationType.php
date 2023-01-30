<?php

declare(strict_types=1);

namespace App\Form\Notification;

use App\Model\Notification\MulticastNotification;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de notification de masse pour tous les etudiants dans un etat du worklow
 */
class MulticastNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Numéro candidat',],
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Nom candidat',],
                'required' => false,
            ])
            ->add('state', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip($options['studentStates']),
                'placeholder' => 'Filtrer par statut du candidat',
                'required' => false,
            ])
            ->add('mediaCode', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip($options['mediaCodes']),
                'placeholder' => 'Filtrer par code media',
                'required' => false,
            ])
            ->add('media', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip($options['mediaStates']),
                'placeholder' => 'Filtrer par statut de media',
                'required' => false,
            ])
            ->add('subject', TextType::class, ['label'=> false, 'attr' => ['placeholder' => 'Sujet du message']])
            ->add('content', CKEditorType::class, ['label'=> false, 'attr' => ['placeholder' => 'Message']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MulticastNotification::class,
            'studentStates' => [],
            'mediaCodes' => [],
            'mediaStates' => [],
        ]);
    }
}
