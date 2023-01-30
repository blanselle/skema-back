<?php

declare(strict_types=1);

namespace App\Form\Admin\User\CV;

use App\Entity\CV\Bac;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BacType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rewardedYear', NumberType::class, [
                'label' => 'Année d\'obtention',
                'row_attr' => ['class' => 'mb-3 rewarded-year']
            ])
            ->add('bacDistinction', EntityType::class, [
                'label' => 'Mention',
                'class' => Bac\BacDistinction::class,
                'choices' => $this->em->getRepository(Bac\BacDistinction::class)->findBy([], ['label' => 'asc']),
                'choice_label' => function ($bacDistinction) {
                    return $bacDistinction->getLabel();
                },
                'required'   => false,
                'empty_data' => null,
            ])
            ->add('bacChannel', EntityType::class, [
                'label' => 'Filière',
                'class' => Bac\BacChannel::class,
                'choices' => $this->em->getRepository(Bac\BacChannel::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($bacChannel) {
                    return $bacChannel->getName();
                },
                'required'   => false,
                'empty_data' => null,
                'row_attr' => ['class' => 'mb-3 bac-channel']
            ])
            ->add('detail', TextType::class, ['label' => 'Bac professionnel', 'required' => false])
            ->add('ine', TextType::class, ['label' => 'INE', 'required' => false])
            ->add('media', MediaType::class, [
                'label' => false,
                'required' => false
            ])
        ;

        $formModifier = function(FormInterface $form, ?Bac\BacChannel $bacChannel, ?int $year = null) {
            $bacTypeChoices = $this->em->getRepository(Bac\BacType::class)->getBacTypesByBacChannel(bacChannel: $bacChannel, year: $year);
            $bacTypeIds = array_map(function($option) { return $option->getId(); }, $bacTypeChoices);
            $bacOptionChoices = $this->em->getRepository(Bac\BacOption::class)->getBacOptionsByBacType($bacTypeIds);
            $form
                ->add('bacTypes', EntityType::class, [
                    'label' => 'Série/Spécialité',
                    'class' => Bac\BacType::class,
                    'choices' => $bacTypeChoices,
                    'choice_label' => function ($bacType) {
                        return $bacType->getName();
                    },
                    'required'   => false,
                    'empty_data' => [],
                    'multiple' => true,
                    'attr' => ['class' => 'mb-3 select2'],
                    'row_attr' => ['class' => 'mb-3 bac-type']
                ])
                ->add('bacOption', EntityType::class, [
                    'label' => 'Option',
                    'class' => Bac\BacOption::class,
                    'choices' => $bacOptionChoices,
                    'choice_label' => function ($bacOption) {
                        return $bacOption->getName();
                    },
                    'required'   => false,
                    'disabled' => count($bacOptionChoices) === 0,
                    'empty_data' => null,
                    'row_attr' => ['class' => 'mb-3 bac-option']
                ])
            ;
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var Bac\Bac|null $bac */
                $bac = $event->getData();
                $formModifier($event->getForm(), $bac?->getBacChannel(), $bac?->getRewardedYear());
            }
        );

        $builder->get('bacChannel')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm()->getParent(), $event->getForm()->getData(), (int)$event->getForm()->getParent()->get('rewardedYear')->getData());
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bac\Bac::class,
            'validation_groups' => ['bo'],
        ]);
    }
}
