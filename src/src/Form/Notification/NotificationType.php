<?php

declare(strict_types=1);

namespace App\Form\Notification;

use App\Constants\User\UserRoleConstants;
use App\Entity\Notification\Notification;
use App\Entity\ProgramChannel;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class NotificationType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $canModifySubject = (bool)$options['canModifySubject'];
        $canModifyReceiver = (bool)$options['canModifyReceiver'];

        $builder
            ->add('receiver', EntityType::class, [
                'label' => 'Destinataire',
                'class' => User::class,
                'required' => false,
                'choices' => $this->em->getRepository(User::class)->findBy([], ['lastName' => 'asc']),
                'choice_label' => function ($user) {
                    return sprintf("[%s] - %s %s", $user->getStudent()?->getIdentifier(), $user->getLastName(), $user->getFirstName());
                },
                'attr' => [
                    'disabled' => !$canModifyReceiver,
                    'class' => 'select2'
                ],
            ])
            ->add('roles', ChoiceType::class, options: [
                'choices' => [
                    'Coordinateur' => UserRoleConstants::ROLE_COORDINATOR,
                    'Responsable' => UserRoleConstants::ROLE_RESPONSABLE,
                    'Administrateur' => UserRoleConstants::ROLE_ADMIN,
                ],
                'required' => false,
                'multiple' => true,
            ])
            ->add('programChannels', EntityType::class, [
                'label' => 'Voie de concours',
                'class' => ProgramChannel::class,
                'required' => false,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'multiple' => true,                
                'attr' => [
                    'class' => 'select2'
                ],
            ])
            ->add('subject', TextType::class, [
                    'label'=> 'Sujet',
                    'attr' => [
                        'placeholder' => 'Sujet du message',
                        'readonly' => $canModifySubject,
                    ],
                ]
            )
            ->add('content', CKEditorType::class, ['label'=> 'Message', 'attr' => ['placeholder' => 'Message']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notification::class,
            'validation_groups' => ['bo'],
            'canModifySubject' => true,
            'canModifyReceiver' => true,
        ]);
    }
}
