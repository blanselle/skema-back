<?php

declare(strict_types=1);

namespace App\Form\Admin\User;

use App\Constants\User\StudentConstants;
use App\Entity\Country;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class StudentType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Masculin'  => StudentConstants::STUDENT_GENDER_MALE,
                    'Féminin'   => StudentConstants::STUDENT_GENDER_FEMALE,
                    'Autre'     => StudentConstants::STUDENT_GENDER_OTHER
                ],
            ])
            ->add('firstNameSecondary', TextType::class, ['label' => 'Prénoms secondaires', 'required' => false])
            ->add('dateOfBirth', DateTimeType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text'
                ])
            ->add('countryBirth', EntityType::class, [
                'label' => 'Pays de naissance',
                'class' => Country::class,
                'choices' => $this->em->getRepository(Country::class)->findBy(['active' => true], ['name' => 'asc']),
                'choice_label' => function ($country) {
                    return $country->getName();
                },
            ])
            ->add('nationality', EntityType::class, [
                'label' => 'Nationalité',
                'class' => Country::class,
                'choices' => $this->em->getRepository(Country::class)->findBy(['active' => true], ['name' => 'asc']),
                'choice_label' => function ($country) {
                    return $country->getNationality();
                },
            ])
            ->add('nationalitySecondary', EntityType::class, [
                'label' => 'Double nationalité',
                'class' => Country::class,
                'required' => false,
                'choices' => $this->em->getRepository(Country::class)->findBy(['active' => true], ['name' => 'asc']),
                'choice_label' => function ($country) {
                    return $country->getNationality();
                },
            ])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('postalCode', TextType::class, ['label' => 'Code postal'])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('country', EntityType::class, [
                'label' => 'Pays',
                'class' => Country::class,
                'choices' => $this->em->getRepository(Country::class)->findBy(['active' => true], ['name' => 'asc']),
                'choice_label' => function ($country) {
                    return $country->getName();
                },
            ])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('identifier', TextType::class, ['label' => 'Numéro candidat', 'disabled' => true])
            ->add('programChannel', EntityType::class, [
                'label' => false,
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['position' => 'asc']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
