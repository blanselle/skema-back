<?php

declare(strict_types=1);

namespace App\Form\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\BacTypeBonus;
use App\Entity\CV\Bac\BacType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BacTypeBonusType extends BasicBonusType
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->defaultBuilder($builder);
        $builder
            ->add('bacType', EntityType::class, [
                'label' => 'Type du bac',
                'class' => BacType::class,
                'choices' => $this->em->getRepository(BacType::class)->findBy([], ['name' => 'ASC']),
                'choice_label' => function ($bacType) {
                    return $bacType->getName();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BacTypeBonus::class,
        ]);
    }
}
