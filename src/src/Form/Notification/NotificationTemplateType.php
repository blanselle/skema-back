<?php

declare(strict_types=1);

namespace App\Form\Notification;

use App\Constants\Notification\NotificationTemplateTagConstants;
use App\Entity\Notification\NotificationTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, ['label'=> 'Sujet', 'attr' => ['placeholder' => 'Sujet du message']])
            ->add('content', CKEditorType::class, ['label'=> 'Message', 'attr' => ['placeholder' => 'Message']])
            ->add('tag', ChoiceType::class, options: [
                'choices' => [
                    'Média transfer' => NotificationTemplateTagConstants::TAG_MEDIA_TRANSFER,
                    'Média rejet' => NotificationTemplateTagConstants::TAG_MEDIA_REJECTION,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NotificationTemplate::class,
        ]);
    }
}
