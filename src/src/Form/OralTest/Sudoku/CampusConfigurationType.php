<?php

namespace App\Form\OralTest\Sudoku;

use App\Entity\OralTest\CampusConfiguration;
use App\Entity\OralTest\DistributionType;
use App\Form\DataTransformer\CampusToNumberTransformer;
use Doctrine\ORM\EntityRepository;
use NumberFormatter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampusConfigurationType extends AbstractType
{
    public function __construct(private CampusToNumberTransformer $transformer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', HiddenType::class, [
                'empty_data' => '',
            ])
            ->add('minimumDurationBetweenTwoTests', IntegerType::class, [
                'required' => true,
                'label' => 'Durée entre les épreuves (candidat)',
                'attr' => ['min' => 0,],
                'rounding_mode' => NumberFormatter::ROUND_DOWN,
            ])
            ->add('distribution', EntityType::class, [
                'class' => DistributionType::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('d')->orderBy('d.position', 'ASC');
                },
                'label' => 'Répartition',
                'choice_label' => 'label',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => ['class' => 'pt-0']
            ])
            ->add('juryDebriefDuration', IntegerType::class, [
                'required' => false,
                'label' => 'Durée debrief (jury)',
                'rounding_mode' => NumberFormatter::ROUND_DOWN,
                'attr' => ['class' => 'jury-debrief-duration nb-of-candidates-event',],
            ])
            ->add('preparationRoom', TextType::class, [
                'required' => false,
                'label' => 'Salle de préparation',
                'attr' => ['min' => 0,],
            ])
            ->add('testConfigurations', CollectionType::class, [
                'entry_type' => TestConfigurationType::class,
                'entry_options' => ['label' => false,],
                'label' => false,
            ])
        ;

        $builder->get('campus')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampusConfiguration::class,
        ]);
    }
}
