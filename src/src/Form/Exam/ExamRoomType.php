<?php

declare(strict_types=1);

namespace App\Form\Exam;

use App\Entity\Exam\ExamRoom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamRoomType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Salle'])
            ->add('numberOfPlaces', NumberType::class, ['label' => 'Nombre de places'])
            ->add('thirdTime', ChoiceType::class, [
                'label' => 'Tiers temps',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'data' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamRoom::class,
        ]);
    }
}
