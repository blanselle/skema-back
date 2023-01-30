<?php

declare(strict_types=1);

namespace App\Form\Faq;

use App\Entity\Faq\FaqTopic;
use App\Entity\ProgramChannel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class FaqTopicType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, options: ['label' => 'Label'])
            ->add('programChannels', EntityType::class, [
                'label' => 'Voie de concours',
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'ASC']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FaqTopic::class,
        ]);
    }
}
