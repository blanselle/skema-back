<?php

namespace App\Form\OralTest\Sudoku;

use App\Repository\CampusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningInfoSearchType extends AbstractType
{
    public function __construct(private CampusRepository $campusRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contestJuryWebsiteCode', ChoiceType::class, [
                'choices' => $this->getContestJuryWebsiteCode(),
                'label' => 'Code campus',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }

    private function getContestJuryWebsiteCode(): array
    {
        $contestJuryWebsiteCode = [];
        $codes = $this->campusRepository->getContestJuryWebsiteCodes();
        foreach ($codes as $code) {
            $contestJuryWebsiteCode[$code] = $code;
        }

        return $contestJuryWebsiteCode;
    }
}