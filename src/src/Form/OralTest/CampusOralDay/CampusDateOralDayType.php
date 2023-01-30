<?php

declare(strict_types=1);

namespace App\Form\OralTest\CampusOralDay;

use App\Entity\Campus;
use App\Helper\DateFormatterHelper;
use App\Repository\OralTest\CampusOralDayRepository;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class CampusDateOralDayType extends AbstractType
{
    public function __construct(
        private CampusOralDayRepository $campusOralDayRepository,
    ){
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $campuses = $this->campusOralDayRepository->getCampusesWithCapacity(
            programChannel: $options['programChannel'],
            firstLanguage: $options['firstLanguage'],
            secondLanguage: $options['secondLanguage'],
        );

        $builder
            ->add('campus', EntityType::class, [
                'choice_label' => 'name',
                'choices' => $campuses,
                'class' => Campus::class,
                'required' => true,
                'constraints' => [new NotNull()],
                'placeholder' => 'Selectionnez un campus',
            ])
        ;

        $formModifier = function(FormInterface $form, Campus $campus = null) {
            
            if(null === $campus) {
                $disable = true;
                $choices = [];
            } else {
                $disable = false;
                $programChannel = $form->getConfig()->getOptions()['programChannel'];
                $firstLanguage = $form->getConfig()->getOptions()['firstLanguage'];
                $secondLanguage = $form->getConfig()->getOptions()['secondLanguage'];
                $choices = $this->campusOralDayRepository->findAllDatesByCampusAndProgramChannelAndLanguages(
                    campus: $campus,
                    programChannel: $programChannel,
                    firstLanguage: $firstLanguage,
                    secondLanguage: $secondLanguage,
                );
            }
    
            $form
                ->add('date', ChoiceType::class, [
                    'disabled' => $disable,
                    'choices' => $choices,
                    'choice_label' => function(DateTimeImmutable $date) {
                        return DateFormatterHelper::formatFull($date);
                    },
                    'required' => true,
                ])
            ;
        };

        $builder->get('campus')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm()->getParent(), $event->getForm()->getData());
            },
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm(), $event->getForm()->get('campus')->getData());
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'programChannel' => null,
            'firstLanguage' => null,
            'secondLanguage' => null,
        ]);
    }
}