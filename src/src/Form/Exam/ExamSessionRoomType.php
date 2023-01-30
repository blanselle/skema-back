<?php

declare(strict_types=1);

namespace App\Form\Exam;

use App\Entity\Campus;
use App\Entity\Exam\ExamSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class ExamSessionRoomType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'disabled' => true,
                'class' => Campus::class,
                'choices' => $this->em->getRepository(Campus::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($campus) {
                    return $campus->getName();
                },
            ])
            ->add('numberOfPlaces', NumberType::class, ['label' => 'Nombre de places total', 'disabled' => true])
            ->add('examRooms', CollectionType::class, [
                'entry_type' => ExamRoomType::class,
                'label' => false,
                'entry_options' => ['label' => false],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamSession::class,
        ]);
    }
}
