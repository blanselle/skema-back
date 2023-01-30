<?php

declare(strict_types=1);

namespace App\Form\Admissibility\Bonus;

use App\Entity\AdministrativeRecord\SportLevel;
use App\Entity\Admissibility\Bonus\SportLevelBonus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SportLevelBonusType extends BasicBonusType
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->defaultBuilder($builder);
        $builder
            ->add('sportLevel', EntityType::class, [
                'label' => 'Niveau sportif',
                'class' => SportLevel::class,
                'choices' => $this->em->getRepository(SportLevel::class)->findBy([], ['position' => 'ASC']),
                'choice_label' => function ($sportLevel) {
                    return $sportLevel->getLabel();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SportLevelBonus::class,
        ]);
    }
}
