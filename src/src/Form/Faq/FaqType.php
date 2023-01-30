<?php

declare(strict_types=1);

namespace App\Form\Faq;

use App\Entity\Faq\Faq;
use App\Entity\Faq\FaqTopic;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class FaqType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, options: ['label' => 'Question'])
            ->add('answer', CKEditorType::class, options: ['label' => 'Réponse'])
            ->add('topics', EntityType::class, [
                'label' => 'Thèmes',
                'class' => FaqTopic::class,
                'choices' => $this->em->getRepository(FaqTopic::class)->findBy([], ['label' => 'ASC']),
                'choice_label' => function ($faqTopic) {
                    return $faqTopic->getLabel();
                },

                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Faq::class,
        ]);
    }
}
