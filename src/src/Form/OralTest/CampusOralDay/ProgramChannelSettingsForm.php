<?php

namespace App\Form\OralTest\CampusOralDay;

use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Entity\ProgramChannel;
use App\Form\DataTransformer\CampusToNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramChannelSettingsForm extends AbstractType
{
    public function __construct(private CampusToNumberTransformer $transformer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', HiddenType::class, [
                'empty_data' => '',
                'required' => true,
            ])
            ->add('programChannels', EntityType::class, [
                'class' => ProgramChannel::class,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('pc')->addOrderBy('pc.position', 'asc');
                },
                'label' => false,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
            ])
        ;

        $builder->get('campus')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampusOralDayConfiguration::class,
        ]);
    }
}