<?php

namespace App\Form\Order;

use App\Constants\Exam\ExamConditionConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Constants\Payment\OrderTypeConstants;
use App\Entity\Exam\ExamSession;
use App\Entity\Payment\Order;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderManualType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de paiement',
                'choices' => [
                    'Frais de concours' => OrderTypeConstants::SCHOOL_REGISTRATION_FEES,
                    'Frais épreuve écrite' => OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION,
                ],
                'disabled' => $options['attr']['disabled'],
            ])
            ->add('student', EntityType::class, [
                'label' => 'Étudiant',
                'class' => Student::class,
                'choices' => $this->em->getRepository(Student::class)->findBy([]),
                'choice_label' => function ($student) {
                    if (null !== $student->getUser()) {
                        return $student->getUser()->getLastName()." ".$student->getUser()->getFirstName();
                    }
                },
                'attr' => [
                    'class' => 'select2',
                ],
                'disabled' => $options['attr']['disabled'],
            ])
            ->add('examSession', EntityType::class, [
                'label' => 'Épreuve écrite',
                'class' => ExamSession::class,
                'choices' => $this->em->getRepository(ExamSession::class)->findBy(['type' => ExamSessionTypeConstants::TYPE_INSIDE]),
                'choice_label' => function ($exam) {
                    /**
                     * @var ExamSession $exam
                     */
                    return sprintf(
                        "%s - %s - %s",
                        $exam->getExamClassification()->getName(),
                        (!empty($exam->getCampus())) ? $exam->getCampus()->getName() : ExamConditionConstants::CONDITION_ONLINE,
                        $exam->getDateStart()->format('d/m/Y h:i')
                    );
                },
                'empty_data' => null,
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ],
                'disabled' => $options['attr']['disabled'],
            ])
            ->add('payments', CollectionType::class, [
                'label' => false,
                'entry_type' => PaymentManualType::class,
                'entry_options' => ['label' => false],
                'disabled' => $options['attr']['disabled'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}