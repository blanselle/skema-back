<?php

declare(strict_types=1);

namespace App\Form;

use App\Constants\Parameters\ParametersKeyTypeConstants;
use App\Entity\Event;
use App\Entity\Parameter\Parameter;
use App\Entity\ProgramChannel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextareaType::class, options: ['label' => 'Label'])
            ->add('paramStart', EntityType::class, [
                'label' => 'Date de début',
                'class' => Parameter::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->join('p.key', 'k')
                        ->andWhere('k.type = :type')
                        ->setParameter('type', ParametersKeyTypeConstants::DATE)
                        ->orderBy('k.name', 'ASC')
                    ;
                },
                'choice_label' => function ($parameter) {
                    return $this->displayChoiceLabel($parameter);
                },
            ])
            ->add('paramEnd', EntityType::class, [
                'label' => 'Date de fin',
                'empty_data'  => null,
                'required' => false,
                'class' => Parameter::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->join('p.key', 'k')
                        ->andWhere('k.type = :type')
                        ->setParameter('type', ParametersKeyTypeConstants::DATE)
                        ->orderBy('k.name', 'ASC')
                    ;
                },
                'choice_label' => function ($parameter) {
                    return $this->displayChoiceLabel($parameter);
                },
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $myEvent = $event->getData();
                $form = $event->getForm();

                $form->add('programChannels', EntityType::class, [
                    'label' => 'Voie de concours',
                    'class' => ProgramChannel::class,
                    'choices' => $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'ASC']),
                    'choice_label' => function ($programChannel) {
                        return $programChannel->getName();
                    },
                    'attr' => [
                        'class' => 'select2',
                    ],
                    'multiple' => true,
                    'data' => (count($myEvent->getProgramChannels()) == 0) ? $this->em->getRepository(ProgramChannel::class)->findBy([], ['name' => 'ASC']) : $myEvent->getProgramChannels()
                ]);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }

    private function displayChoiceLabel(Parameter $parameter): string
    {
        $programChannels = [];
        $campuses = [];
        foreach ($parameter->getProgramChannels() as $item) {
            $programChannels[] = $item->getName();
        }
        foreach ($parameter->getCampuses() as $item) {
            $campuses[] = $item->getName();
        }
        return sprintf(
            '%s : %s -[%s] [%s]',
            $parameter->getKey()->getName(),
            $parameter->getValueDateTime()?->format('Y-m-d H:i'),
            implode(', ', $programChannels),
            implode(', ', $campuses),
        );
    }
}
