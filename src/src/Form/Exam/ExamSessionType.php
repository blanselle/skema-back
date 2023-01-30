<?php

declare(strict_types=1);

namespace App\Form\Exam;

use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Campus;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamSessionType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('examClassification', EntityType::class, [
                'label' => 'Sélectionnez une épreuve',
                'class' => ExamClassification::class,
                'choices' => $this->em->getRepository(ExamClassification::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($examClassification) {
                    return $examClassification->getName();
                },
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choices' => $this->em->getRepository(Campus::class)->findBy(['assignmentCampus' => true], ['name' => 'asc']),
                'choice_label' => function ($campus) {
                    return $campus->getName();
                },
                'required' => false
            ])
            ->add('dateStart', DateTimeType::class, ['label' => 'Date et heure de début de la session', 'widget' => 'single_text'])
            ->add('dateEnd', DateTimeType::class, ['label' => 'Date et heure de fin de la session', 'widget' => 'single_text', 'required' => false])
            ->add('numberOfPlaces', NumberType::class, ['label' => 'Nombre de places'])
            ->add('price', TextType::class, ['label' => 'Tarif (facultatif)', 'required' => false])
            ->add('priceLink', TextType::class, ['label' => 'Lien paiement (facultatif)', 'required' => false])
            ->add('type', HiddenType::class, ['data' => ExamSessionTypeConstants::TYPE_INSIDE])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamSession::class,
        ]);
    }
}
