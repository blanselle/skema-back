<?php

declare(strict_types=1);

namespace App\Form\Admissibility;

use App\Constants\Admissibility\AdmissibilityConstants;
use App\Entity\Admissibility\Admissibility;
use App\Entity\Exam\ExamClassification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdmissibilityType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('examClassification', EntityType::class, [
                'label' => false,
                'class' => ExamClassification::class,
                'choices' => $this->em->getRepository(ExamClassification::class)->findBy([], ['name' => 'asc']),
                'choice_label' => function ($examClassification) {
                    return $examClassification->getName();
                },
                'disabled' => $options['attr']['disabled']
            ])
            ->add('type', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    '' => '',
                    'Saisie des bornes' => AdmissibilityConstants::CALCUL_WITH_BORDERS,
                    'Médiane' => AdmissibilityConstants::CALCUL_WITH_MEDIAN,
                    'Import statique' => AdmissibilityConstants::CALCUL_WITH_IMPORT,
                ],
                'disabled' => $options['attr']['disabled']
            ])
            ->add('params', CollectionType::class, [
                'label' => false,
                'entry_type' => ParamType::class,
                'entry_options' => ['label' => false],
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        parent::finishView($view, $form, $options);

        // order params by programChannel name
        usort($view['params']->children, function(FormView $a, FormView $b) {
            $objectA = $a->vars['data'];
            $objectB = $b->vars['data'];
            $posA = $objectA->getProgramChannel()->getName();
            $posB = $objectB->getProgramChannel()->getName();

            if ($posA == $posB) {
                return 0;
            }

            return ($posA < $posB) ? -1 : 1;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admissibility::class,
        ]);
    }
}
