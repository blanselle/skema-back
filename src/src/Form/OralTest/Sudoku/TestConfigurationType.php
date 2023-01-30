<?php

namespace App\Form\OralTest\Sudoku;

use App\Entity\OralTest\TestConfiguration;
use App\Form\DataTransformer\TestTypeToNumberTransformer;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestConfigurationType extends AbstractType
{
    public function __construct(private TestTypeToNumberTransformer $transformer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('durationOfTest', IntegerType::class, [
                'required' => true,
                'label' => 'Durée de l\'épreuve',
                'attr' => ['min' => 0, 'class' => 'duration-of-test nb-of-candidates-event',],
                'rounding_mode' => NumberFormatter::ROUND_DOWN,
            ])
            ->add('preparationTime', IntegerType::class, [
                'label' => 'Durée de préparation',
                'attr' => ['min' => 0, 'class' => 'preparation-time nb-of-candidates-event',],
                'rounding_mode' => NumberFormatter::ROUND_DOWN,
            ])
            ->add('eveningEvent', CheckboxType::class, [
                'label' => 'Epreuve en soirée',
                'required' => false,
            ])
            ->add('testType', HiddenType::class, [
                'empty_data' => '',
            ])
            ->add('slotConfigurations', CollectionType::class, [
                'entry_type' => SlotConfigurationType::class,
                'entry_options' => ['label' => false,],
                'label' => false,
            ])
        ;

        $builder->get('testType')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TestConfiguration::class,
        ]);
    }
}
