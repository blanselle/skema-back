<?php

declare(strict_types=1);

namespace App\Form\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\BasicBonus;
use App\Entity\ProgramChannel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasicBonusType extends AbstractType
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->defaultBuilder($builder);
    }

    protected function defaultBuilder(FormBuilderInterface &$builder): void
    {
        $builder
            ->add('value', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 20,
                    'step' => 0.01,
                    'placeholder' => '0.00'
                ]
            ])
            ->add('programChannel', EntityType::class, [
                'label' => 'Voie de concours',
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'ASC']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BasicBonus::class,
        ]);
    }
}
