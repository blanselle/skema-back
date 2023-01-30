<?php

namespace App\Form\Order;

use App\Constants\Payment\PaymentModeConstants;
use App\Entity\Payment\Payment;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentManualType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode de paiement',
                'choices' => PaymentModeConstants::PAYMENT_MANUAL_LIST
            ])
            ->add('additionalInformation', CKEditorType::class, [
                'label' => 'Informations complémentaires',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}