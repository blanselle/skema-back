<?php

declare(strict_types=1);

namespace App\Form\Admissibility\Bonus;

use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Entity\Admissibility\Bonus\ExperienceBonus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceBonusType extends BasicBonusType
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->defaultBuilder($builder);
        $builder
            ->add('duration', NumberType::class)
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'expérience',
                'choices' => [
                    'Expérience professionnelle' => ExperienceTypeConstants::TYPE_PROFESSIONAL,
                    'Expérience associative' => ExperienceTypeConstants::TYPE_ASSOCIATIVE,
                    'Expérience internationale' => ExperienceTypeConstants::TYPE_INTERNATIONAL,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExperienceBonus::class,
        ]);
    }
}
