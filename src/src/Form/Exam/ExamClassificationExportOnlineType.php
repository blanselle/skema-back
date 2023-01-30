<?php

namespace App\Form\Exam;

use App\Entity\Exam\ExamSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamClassificationExportOnlineType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('examSession', EntityType::class, [
                'label' => false,
                'class' => ExamSession::class,
                'choices' => $this->em->getRepository(ExamSession::class)->getExamSessionsOnline(false),
                'choice_label' => function ($exam) {
                    /**
                     * @var ExamSession $exam
                     */
                    return sprintf("%s - %s", $exam->getExamClassification()->getName(), $exam->getDateStart()->format('Y/m/d'));
                },
                'placeholder' => 'Toutes les sessions',
                'empty_data' => null,
                'required' => false,
                'mapped' => false
            ])
            ->add('delay', IntegerType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Délai'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}