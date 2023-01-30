<?php

declare(strict_types=1);

namespace App\Form\Exam;

use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamCondition;
use App\Entity\Exam\ExamSessionType;
use App\Entity\ProgramChannel;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class ExamClassificationType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('examSessionType', EntityType::class, [
                'label' => 'Type d\'épreuve',
                'class' => ExamSessionType::class,
                'choices' => $this->em->getRepository(ExamSessionType::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($type) {
                    return $type->getName();
                },
                'expanded' => true,
            ])
            ->add('examCondition', EntityType::class, [
                'label' => 'Conditions de passage',
                'class' => ExamCondition::class,
                'choices' => $this->em->getRepository(ExamCondition::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($condition) {
                    return $condition->getName();
                },
                'expanded' => true,
            ])
            ->add('programChannels', EntityType::class, [
                'label' => 'Voie de concours',
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'ASC']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'multiple' => true,
            ])
            ->add('equipment', CKEditorType::class, options: ['label' => 'Matériel'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamClassification::class,
        ]);
    }
}
