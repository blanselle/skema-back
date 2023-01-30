<?php

declare(strict_types=1);

namespace App\Form\Admin\User\CV;

use App\Entity\CV\SchoolReport;
use App\Form\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('scoreRetained', NumberType::class, ['label' => 'Moyenne retenue', 'required' => false])
            ->add('scoreNotOutOfTwenty', CheckboxType::class, [
                'label' => 'Ma moyenne n\'est pas sur 20',
                'required' => false,
                'row_attr' => [
                    'class' => 'score-not-out-of-twenty',
                ],
            ])
            ->add('media', MediaType::class, [
                'label' => false,
                'required' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $schoolReport = $event->getData();
            $form = $event->getForm();

            $form
                ->add('score', NumberType::class, [
                    'label' => 'Moyenne candidat',
                    'row_attr' => [
                        'class' => 'school-report-score',
                    ],
                    'required' => false,
                    'disabled' => ($schoolReport?->isScoreNotOutOfTwenty() === true),
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolReport::class,
        ]);
    }
}
