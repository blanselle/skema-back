<?php

declare(strict_types=1);

namespace App\Form\Exam;

use App\Entity\Exam\ExamRoom;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamStudentRoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('examRoom', EntityType::class, [
                'label' => 'Salle',
                'class' => ExamRoom::class,
                'query_builder' => function (EntityRepository $em) use ($options) {
                    return $em->createQueryBuilder('r')
                        ->innerJoin(ExamSession::class, 'e', 'WITH', 'r MEMBER OF e.examRooms')
                        ->where('e.id = :examSession')
                        ->setParameter('examSession', $options['data']->getExamSession())
                        ->orderBy('r.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamStudent::class,
        ]);
    }
}
