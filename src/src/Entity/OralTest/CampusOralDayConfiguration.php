<?php

namespace App\Entity\OralTest;

use App\Entity\Campus;
use App\Entity\Exam\ExamLanguage;
use App\Entity\ProgramChannel;
use App\Repository\OralTest\CampusOralDayConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CampusOralDayConfigurationRepository::class)]
#[ORM\Table(name: 'oral_test_campus_oral_day_configuration')]
class CampusOralDayConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'slot:collection:read',
        'oralTestStudent:collection:read',
    ])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $optionalLv1 = null;

    #[ORM\Column(nullable: true)]
    private ?bool $optionalLv2 = null;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class)]
    #[ORM\JoinTable(name: 'oral_test_campus_oral_day_configuration_program_channel')]
    private Collection $programChannels;

    #[ORM\ManyToOne(targetEntity: Campus::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'slot:collection:read',
        'oralTestStudent:collection:read'
    ])]
    private ?Campus $campus = null;

    #[ORM\ManyToMany(targetEntity: ExamLanguage::class)]
    #[ORM\JoinTable(name: 'oral_test_campus_oral_day_configuration_first_languages')]
    private Collection $firstLanguages;

    #[ORM\ManyToMany(targetEntity: ExamLanguage::class)]
    #[ORM\JoinTable(name: 'oral_test_campus_oral_day_configuration_second_languages')]
    private Collection $secondLanguages;

    #[ORM\OneToMany(mappedBy: 'configuration', targetEntity: CampusOralDay::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $campusOralDays;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
        $this->firstLanguages = new ArrayCollection();
        $this->secondLanguages = new ArrayCollection();
        $this->campusOralDays = new ArrayCollection();
    }

    public function __toString(): string
    {
        $str = "{$this->campus->getName()} - ";
        foreach ($this->programChannels as $programChannel) {
            $str .= "{$programChannel->getName()}, ";
        }

        return rtrim($str, ', ');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isOptionalLv1(): ?bool
    {
        return $this->optionalLv1;
    }

    public function setOptionalLv1(bool $optionalLv1): self
    {
        $this->optionalLv1 = $optionalLv1;

        return $this;
    }

    public function isOptionalLv2(): ?bool
    {
        return $this->optionalLv2;
    }

    public function setOptionalLv2(bool $optionalLv2): self
    {
        $this->optionalLv2 = $optionalLv2;

        return $this;
    }

    /**
     * @return Collection<int, ProgramChannel>
     */
    public function getProgramChannels(): Collection
    {
        return $this->programChannels;
    }

    public function addProgramChannel(ProgramChannel $programChannel): self
    {
        if (!$this->programChannels->contains($programChannel)) {
            $this->programChannels->add($programChannel);
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannels->removeElement($programChannel);

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, ExamLanguage>
     */
    public function getFirstLanguages(): Collection
    {
        return $this->firstLanguages;
    }

    public function addFirstLanguage(ExamLanguage $firstLanguage): self
    {
        if (!$this->firstLanguages->contains($firstLanguage)) {
            $this->firstLanguages->add($firstLanguage);
        }

        return $this;
    }

    public function removeFirstLanguage(ExamLanguage $firstLanguage): self
    {
        $this->firstLanguages->removeElement($firstLanguage);

        return $this;
    }

    /**
     * @return Collection<int, ExamLanguage>
     */
    public function getSecondLanguages(): Collection
    {
        return $this->secondLanguages;
    }

    public function addSecondLanguage(ExamLanguage $secondLanguage): self
    {
        if (!$this->secondLanguages->contains($secondLanguage)) {
            $this->secondLanguages->add($secondLanguage);
        }

        return $this;
    }

    public function removeSecondLanguage(ExamLanguage $secondLanguage): self
    {
        $this->secondLanguages->removeElement($secondLanguage);

        return $this;
    }

    /**
     * @return Collection<int, CampusOralDay>
     */
    public function getCampusOralDays(): Collection
    {
        return $this->campusOralDays;
    }

    public function addCampusOralDay(CampusOralDay $campusOralDay): self
    {
        if (!$this->campusOralDays->contains($campusOralDay)) {
            $this->campusOralDays->add($campusOralDay);
            $campusOralDay->setConfiguration($this);
        }

        return $this;
    }

    public function removeCampusOralDay(CampusOralDay $campusOralDay): self
    {
        if ($this->campusOralDays->removeElement($campusOralDay)) {
            // set the owning side to null (unless already changed)
            if ($campusOralDay->getConfiguration() === $this) {
                $campusOralDay->setConfiguration(null);
            }
        }

        return $this;
    }
}
