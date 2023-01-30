<?php

declare(strict_types=1);

namespace App\Form\Admissibility;

use App\Entity\Admissibility\Param;
use App\Entity\ProgramChannel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParamType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('programChannel', EntityType::class, [
                'label' => false,
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'disabled' => 'disabled'
            ])
            ->add('highClipping', NumberType::class, ['label' => '% écrêtage haut', 'required' => false])
            ->add('lowClipping', NumberType::class, ['label' => '% écrêtage bas', 'required' => false])
            ->add('median', NumberType::class, ['label' => 'Médiane'])
            ->add('file', FileType::class, ['label' => false, 'mapped' => false])
            ->add('borders', CollectionType::class, [
                'label' => false,
                'entry_type' => BorderType::class,
                'entry_options' => ['label' => false],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Param::class,
        ]);
    }
}
