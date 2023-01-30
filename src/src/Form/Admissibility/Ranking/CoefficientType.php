<?php

declare(strict_types=1);

namespace App\Form\Admissibility\Ranking;

use App\Constants\Admissibility\Ranking\CoefficientTypeConstants;
use App\Entity\Admissibility\Ranking\Coefficient;
use App\Entity\ProgramChannel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoefficientType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'CV' => CoefficientTypeConstants::TYPE_CV,
                    'Anglais' => CoefficientTypeConstants::TYPE_ENG,
                    'Management' => CoefficientTypeConstants::TYPE_MNGT,
                ]
            ])
            ->add('coefficient', NumberType::class, ['label' => 'Coefficient'])
            ->add('programChannel', EntityType::class, [
                'label' => false,
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coefficient::class,
        ]);
    }
}