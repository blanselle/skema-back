<?php

declare(strict_types=1);

namespace App\Form\Admin\User\CV;

use App\Entity\CV\Cv;
use App\Entity\CV\Language;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CvType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bac', BacType::class, [
                'disabled' => $options['attr']['exemption'], 
                'required' => false,
            ])
            ->add('bacSups', CollectionType::class, [
                'entry_type' => BacSupType::class,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                    'programChannel' => $options['programChannel']
                ],
                'disabled' => $options['attr']['exemption'],
                'allow_extra_fields' => true
            ])
            ->add('languages', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Language::class,
                'choices' => $this->em->getRepository(Language::class)->findBy([], ['label' => 'asc']),
                'choice_label' => function ($language) {
                    return $language->getLabel();
                },
                'multiple' => true,
                'disabled' => $options['attr']['exemption'],
                'attr' => [
                    'class' => 'select2',
                ]
            ])
            ->add('experiences', CollectionType::class, [
                'entry_type' => ExperienceType::class,
                'label' => false,
                'entry_options' => ['label' => false],
                'disabled' => $options['attr']['exemption'],
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cv::class,
            'validation_groups' => ['bo'],
            'programChannel' => null
        ]);
    }
}
