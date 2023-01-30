<?php

declare(strict_types=1);

namespace App\Form\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\LanguageBonus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageBonusType extends BasicBonusType
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->defaultBuilder($builder);
        $builder
            ->add('min', NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LanguageBonus::class,
        ]);
    }
}
