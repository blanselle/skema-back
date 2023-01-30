<?php

declare(strict_types=1);

namespace App\Form\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\BacDistinctionBonus;
use App\Entity\CV\Bac\BacDistinction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BacDistinctionBonusType extends BasicBonusType
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->defaultBuilder($builder);
        $builder
        ->add('bacDistinction', EntityType::class, [
            'label' => 'Mention',
            'class' => BacDistinction::class,
            'choices' => $this->em->getRepository(BacDistinction::class)->findBy([], ['id' => 'asc']),
            'choice_label' => function ($bacDistinction) {
                return $bacDistinction->getLabel();
            }
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BacDistinctionBonus::class,
        ]);
    }
}
