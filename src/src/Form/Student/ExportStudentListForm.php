<?php

namespace App\Form\Student;

use App\Manager\StudentExportManager;
use App\Model\Student\ExportChoiceModel;
use App\Model\Student\ExportStudentListModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportStudentListForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('columns', ChoiceType::class, [
                'label' => 'Selectionnez les colonnes à exporter',
                'choices' => $this->getChoices(),
                'choice_label' => ChoiceList::label($this, function(?ExportChoiceModel $choice) {
                    return (null !== $choice)? $choice->getLabel() : '';
                }),
                'choice_value' => ChoiceList::value($this, function(?ExportChoiceModel $choice) {
                    return (null !== $choice)? $choice->getValue() : '';
                }),
                'required'   => false,
                'empty_data' => [],
                'multiple' => true,
                'attr' => [
                    'class' => 'export-student-list-columns-select2',
                ]
            ])
            ->add('identifier', HiddenType::class)
            ->add('lastname', HiddenType::class)
            ->add('media', HiddenType::class)
            ->add('mediaCode', HiddenType::class)
            ->add('state', HiddenType::class)
            ->add('intern', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExportStudentListModel::class
        ]);
    }

    private function getChoices(): array
    {
        return StudentExportManager::getChoices();
    }
}