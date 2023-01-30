<?php

namespace App\Form\OralTest\CampusOralDay;

use App\Entity\Exam\ExamLanguage;
use App\Entity\OralTest\CampusOralDayConfiguration;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageSettingsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('optionalLv1', CheckboxType::class, [
                'label' => 'oral_test.campus_oral_day_settings_languages.form.fields.optional',
                'row_attr' => ['class' => 'mb-0'],
                'required' => false,
            ])
            ->add('optionalLv2', CheckboxType::class, [
                'label' => 'oral_test.campus_oral_day_settings_languages.form.fields.optional',
                'row_attr' => ['class' => 'mb-0'],
                'required' => false,
            ])
            ->add('firstLanguages', EntityType::class, [
                'class' => ExamLanguage::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('el')->orderBy('el.name', 'asc');
                },
                'label' => 'oral_test.campus_oral_day_settings_languages.form.fields.first_language',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('secondLanguages', EntityType::class, [
                'class' => ExamLanguage::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('el')->orderBy('el.name', 'asc');
                },
                'label' => 'oral_test.campus_oral_day_settings_languages.form.fields.second_language',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampusOralDayConfiguration::class,
        ]);
    }
}