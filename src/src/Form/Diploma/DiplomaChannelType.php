<?php

declare(strict_types=1);

namespace App\Form\Diploma;

use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Repository\Diploma\DiplomaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiplomaChannelType extends AbstractType
{
    public function __construct(private DiplomaRepository $diplomaRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('diplomas', EntityType::class, [
                'label' => 'Diplômes',
                'class' => Diploma::class,
                'choices' => $this->diplomaRepository->findBy([], ['name' => 'asc']),
                'choice_label' => function ($diploma) {
                    return $diploma->getName();
                },
                'multiple' => true,
                'required' => false,
            ])
            ->add('needDetail', CheckboxType::class, [
                'label' => 'Besoin de précision *',
                'help' => '* Ce champ permet de spécifier si la filière necessite une description supplémenatire lors de l\'inscription d\'un candidat',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiplomaChannel::class,
        ]);
    }
}
