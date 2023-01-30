<?php

namespace App\Form\OralTest\Sudoku;

use App\Entity\OralTest\SudokuConfiguration;
use App\Entity\ProgramChannel;
use App\Repository\ProgramChannelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SudokuConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('programChannels', EntityType::class, [
                'class' => ProgramChannel::class,
                'query_builder' => function (ProgramChannelRepository $repository) {
                    return $repository->getRemainingSudokuProgramChannelsQuery();
                },
                'label' => 'Voie de concours',
                'choice_label' => 'name',
                'by_reference' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SudokuConfiguration::class
        ]);
    }
}