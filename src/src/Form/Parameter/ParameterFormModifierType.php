<?php

declare(strict_types=1);

namespace App\Form\Parameter;

use App\Entity\Parameter\Parameter;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

final class ParameterFormModifierType
{
    public function formModifierText(FormInterface $form, Parameter $parameter = null): void
    {
        $form->add('value', TextType::class, options: [
            'label' => 'Valeur',
            'getter' => function (Parameter $parameter): string {
                return $parameter->getValueString() ?? "TESTU";
            },
            'setter' => function (Parameter &$parameter, ?string $value): void {
                $parameter->setValueString($value);
            },
        ]);
    }

    public function formModifierDate(FormInterface $form, Parameter $parameter = null): void
    {
        $form->add('value', DateTimeType::class, options: [
            'label' => 'Valeur',
            'widget' => 'single_text',
            'getter' => function (Parameter $parameter): DateTimeInterface {
                return $parameter->getValueDateTime() ?? new DateTime('now');
            },
            'setter' => function (Parameter &$parameter, ?DateTime $value): void {
                $parameter->setValueDateTime($value);
            },
        ]);
    }

    public function formModifierNumber(FormInterface $form, Parameter $parameter = null): void
    {
        $form->add('value', IntegerType::class, options: [
            'label' => 'Valeur',
            'getter' => function (Parameter $parameter): int {
                return $parameter->getValueNumber() ?? 0;
            },
            'setter' => function (Parameter &$parameter, ?int $value): void {
                $parameter->setValueNumber($value);
            },
        ]);
    }
}
