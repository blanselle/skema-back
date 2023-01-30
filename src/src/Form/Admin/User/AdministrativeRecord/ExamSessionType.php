<?php

declare(strict_types=1);

namespace App\Form\Admin\User\AdministrativeRecord;

use App\Entity\Campus;
use App\Entity\Exam\ExamSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Constants\Exam\ExamSessionTypeConstants;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ExamSessionType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('examClassification', ExamClassificationType::class, ['label' => false])
            ->add('dateStart', DateTimeType::class, ['label' => 'Date et heure de début de la session', 'widget' => 'single_text'])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event)  {
                $form = $event->getForm();
                /** @var ExamSession $examSession */
                $examSession = $event->getData();

                $form->add('type', HiddenType::class, ['data' => (empty($examSession->getType())) ? ExamSessionTypeConstants::TYPE_INSIDE : $examSession->getType()]);

                if ($examSession->getType() === ExamSessionTypeConstants::TYPE_INSIDE) {
                    $form
                        ->add('numberOfPlaces', NumberType::class, ['label' => 'Nombre de places'])
                        ->add('campus', EntityType::class, [
                            'label' => 'Campus',
                            'class' => Campus::class,
                            'choices' => $this->em->getRepository(Campus::class)->findBy([], ['name' => 'asc']),
                            'choice_label' => function ($campus) {
                                return $campus->getName();
                            },
                            'required' => false
                        ])
                        ->add('dateEnd', DateTimeType::class, ['label' => 'Date et heure de fin de la session', 'widget' => 'single_text'])
                        ->add('price', NumberType::class, ['label' => 'Prix'])
                        ->add('priceLink', TextType::class, ['label' => 'Lien paiement'])
                    ;
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamSession::class,
        ]);
    }
}
