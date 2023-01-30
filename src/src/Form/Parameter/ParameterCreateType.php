<?php

declare(strict_types=1);

namespace App\Form\Parameter;

use App\Entity\Parameter\Parameter;
use App\Entity\Parameter\ParameterKey;
use App\Repository\Parameter\ParameterKeyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterCreateType extends AbstractType
{
    public function __construct(
        private ParameterKeyRepository $parameterKeyRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', ChoiceType::class, [
                'label' => 'Type du paramètre',
                'choices'      => $this->parameterKeyRepository->findAll(),
                'choice_label' => static function (?ParameterKey $parameter) {
                    return null === $parameter ? '' : $parameter->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parameter::class,
            'validation_groups' => false,
        ]);
    }
}
