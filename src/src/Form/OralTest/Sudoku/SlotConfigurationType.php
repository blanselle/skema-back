<?php

namespace App\Form\OralTest\Sudoku;

use App\Entity\OralTest\SlotConfiguration;
use App\Form\DataTransformer\SlotTypeToNumberTransformer;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotConfigurationType extends AbstractType
{
    public function __construct(private SlotTypeToNumberTransformer $transformer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', TimeType::class, [
                'input' => 'datetime_immutable',
                'required' => false,
                'empty_data' => null,
                'widget' => 'single_text',
                'label' => 'Heure de début',
                'attr' => ['class' => 'start-time nb-of-candidates-event',],
            ])
            ->add('endTime', TimeType::class, [
                'input' => 'datetime_immutable',
                'required' => false,
                'empty_data' => null,
                'widget' => 'single_text',
                'label' => 'Heure de fin',
                'attr' => ['class' => 'end-time nb-of-candidates-event',],
            ])
            ->add('breakTime', TimeType::class, [
                'input' => 'datetime_immutable',
                'required' => false,
                'empty_data' => null,
                'widget' => 'single_text',
                'label' => 'Heure de pause',
                'attr' => ['class' => 'break-time nb-of-candidates-event',],
            ])
            ->add('breakDuration', IntegerType::class, [
                'required' => false,
                'label' => 'Durée de pause',
                'attr' => ['min' => 0, 'class' => 'break-duration nb-of-candidates-event',],
                'rounding_mode' => NumberFormatter::ROUND_DOWN,
            ])
            ->add('nbOfCandidatesPerJury', IntegerType::class, [
                'required' => false,
                'label' => 'Candidats / Jury',
                'attr' => ['min' => 0, 'readonly' => true, 'class' => 'nb-of-candidates-per-jury',],
            ])
            ->add('slotType', HiddenType::class, [
                'empty_data' => '',
            ])
        ;

        $builder->get('slotType')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SlotConfiguration::class,
        ]);
    }
}
