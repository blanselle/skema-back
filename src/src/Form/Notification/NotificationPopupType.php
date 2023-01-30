<?php

declare(strict_types=1);

namespace App\Form\Notification;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class NotificationPopupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('content', CKEditorType::class, ['label'=> false])
        ;
    }
}
