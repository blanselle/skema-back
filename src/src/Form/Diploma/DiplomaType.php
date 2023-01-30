<?php

declare(strict_types=1);

namespace App\Form\Diploma;

use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Entity\ProgramChannel;
use App\Repository\Diploma\DiplomaChannelRepository;
use App\Repository\ProgramChannelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiplomaType extends AbstractType
{
    public function __construct(
        private DiplomaChannelRepository $diplomaChannelRepository,
        private ProgramChannelRepository $programChannelRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('diplomaChannels', EntityType::class, [
                'label' => 'Filières',
                'class' => DiplomaChannel::class,
                'choices' => $this->diplomaChannelRepository->findBy([], ['name' => 'asc']),
                'choice_label' => function ($diplomaChannel) {
                    return $diplomaChannel->getName();
                },
                'multiple' => true,
                'required' => false,
            ])
            ->add('programChannels', EntityType::class, [
                'label' => 'Voie de concours',
                'class' => ProgramChannel::class,
                'choices' => $this->programChannelRepository->findBy([], ['name' => 'asc']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'multiple' => true,
                'required' => false,
            ])
            ->add('needDetail', CheckboxType::class, [
                'label' => 'Besoin de précision *',
                'help' => '* Ce champ permet de spécifier si un diplome necessite une description supplémenatire lors de l\'inscription d\'un candidat',
                'required' => false,
            ])
            ->add('additional', CheckboxType::class, [
                'label' => 'Visible que pour les doubles parcours',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diploma::class,
        ]);
    }
}
