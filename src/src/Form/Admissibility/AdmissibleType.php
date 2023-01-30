<?php

namespace App\Form\Admissibility;

use App\Entity\ProgramChannel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdmissibleType extends AbstractType
{
    public const BUTTON_SAVE = 'save';
    public const BUTTON_EXPORT = 'export';

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('programChannels', EntityType::class, [
                'label' => 'Voies de concours',
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'expanded' => true,
                'multiple' => true,
                'mapped' => false
            ])
            ->add('score', NumberType::class, ['label' => 'Score / Barre d’admissibilité', 'mapped' => false])
            ->add('simulate', SubmitType::class, [
                'label' => 'Simuler les admissibles',
                'attr' => ['disabled' => $options['calculatorIsRunning']]
            ])
            ->add(self::BUTTON_SAVE, SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => ['disabled' => $options['calculatorIsRunning']]
            ])
            ->add(self::BUTTON_EXPORT, SubmitType::class, [
                'label' => 'Exporter',
                'attr' => ['disabled' => $options['calculatorIsRunning']]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'calculatorIsRunning' => false,
        ]);
    }
}