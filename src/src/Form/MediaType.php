<?php

declare(strict_types=1);

namespace App\Form;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formFile', FileType::class, [
                'label' => 'Justificatif',
                'required' => false,
                'mapped' => true,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg', 'image/png', 'image/svg+xml', 'image/gif',
                        ],
                        'mimeTypesMessage' => 'L\'image doit être dans l\'un des formats suivants : {{ types }}'
                    ])
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {

                /** @var Media $media */
                $media = $event->getData();
                $form = $event->getForm();

                if (null != $media && !empty($media->getOriginalName()) && $media->getState() === MediaWorflowStateConstants::STATE_ACCEPTED) {
                    $form->add('originalName', TextType::class, [
                        'label' => 'Image',
                        'attr' => [
                            'readonly' => true
                        ],
                        'mapped' => false,
                    ]);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
