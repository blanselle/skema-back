<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ProgramChannel;
use App\Entity\Program;
use App\Repository\ProgramRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramChannelType extends AbstractType
{
    public function __construct(private ProgramRepository $programRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: ['label' => 'Nom'])
            ->add('program', EntityType::class, [
                'label' => 'Programme',
                'class' => Program::class,
                'choices' => $this->programRepository->findAll(),
                'choice_label' => function ($program) {
                    return $program->getName();
                }
            ])
            ->add('position', IntegerType::class, options: [
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('intern', options: ['label' => 'Cette session est gérée en interne'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProgramChannel::class,
        ]);
    }
}
