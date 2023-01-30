<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Bloc\Bloc;
use App\Entity\Bloc\BlocTag;
use App\Entity\ProgramChannel;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlocType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', options: [
                'label' => 'Label',
                'empty_data' => '',
            ])
            ->add('media', MediaType::class, [
                'label' => false
            ])
            ->add('content', CKEditorType::class, options: ['label' => 'Contenu'])
            ->add('link', options: [
                'label' => 'Lien',
                'empty_data' => '',
            ])
            ->add('labelLink', options: [
                'label' => 'Label de lien',
                'empty_data' => '',
            ])
            ->add('position', NumberType::class, ['label' => 'Position'])
            ->add('active', CheckboxType::class, ['label' => 'Active', 'required' => false])
            ->add('tag', EntityType::class, [
                'label' => 'Tag',
                'class' => BlocTag::class,
                'choices' => $this->em->getRepository(BlocTag::class)->findAll(),
                'choice_label' => function ($blocTag) {
                    return $blocTag->getLabel();
                }
            ])
            ->add('programChannels', EntityType::class, [
                'label' => 'Voie de concours',
                'required' => false,
                'class' => ProgramChannel::class,
                'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'ASC']),
                'choice_label' => function ($programChannel) {
                    return $programChannel->getName();
                },
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bloc::class,
        ]);
    }
}
