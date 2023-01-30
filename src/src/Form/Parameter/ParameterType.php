<?php

declare(strict_types=1);

namespace App\Form\Parameter;

use App\Constants\Parameters\ParametersKeyTypeConstants;
use App\Entity\Campus;
use App\Entity\Parameter\Parameter;
use App\Entity\ProgramChannel;
use App\Repository\CampusRepository;
use App\Repository\ProgramChannelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterType extends AbstractType
{
    public function __construct(
        private CampusRepository $campusRepository,
        private ProgramChannelRepository $programChannelRepository,
        private ParameterFormModifierType $parameterFormModifierType,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', TextType::class, [
                'label' => 'Type du paramètre',
                'getter' => function (Parameter $parameter): string {
                    return $parameter->getKey()->getName();
                },
                'disabled' => true,
            ])
            ->add('programChannels', EntityType::class, [
                'label' => 'Voie de concours',
                'class' => ProgramChannel::class,
                'choices' => $this->programChannelRepository->findBy([], ['name' => 'ASC']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'multiple' => true,
                'required' => false,
            ])
            ->add('campuses', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choices' => $this->campusRepository->findBy([], ['name' => 'ASC']),
                'choice_label' => function ($campus) {
                    return $campus->getName();
                },
                'multiple' => true,
                'required' => false,
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                // this would be your entity, i.e. SportMeetup
                $parameter = $event->getData();

                $this->formModifier($event->getForm(), $parameter);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parameter::class,
        ]);
    }

    private function formModifier(FormInterface $form, Parameter $parameter = null): void
    {
        switch ($parameter->getKey()->getType()) {
            case ParametersKeyTypeConstants::TEXT:
                $this->parameterFormModifierType->formModifierText($form, $parameter);
                break;

            case ParametersKeyTypeConstants::DATE:
                $this->parameterFormModifierType->formModifierDate($form, $parameter);
                break;

            case ParametersKeyTypeConstants::NUMBER:
                $this->parameterFormModifierType->formModifierNumber($form, $parameter);
                break;
        }
    }
}
