<?php

declare(strict_types=1);

namespace App\Entity\Faq;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ProgramChannel;
use App\Entity\Traits\DateTrait;
use App\Repository\Faq\FaqTopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FaqTopicRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['faqTopic:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['faqTopic:item:read']]
        ],
    ],
)]
class FaqTopic
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['faqTopic:collection:read', 'faqTopic:item:read'])]
    private string $label;

    #[ORM\ManyToMany(targetEntity: programChannel::class)]
    #[Groups(['faqTopic:collection:read', 'faqTopic:item:read'])]
    private Collection $programChannels;

    #[ORM\ManyToMany(targetEntity: Faq::class, mappedBy: 'topics')]
    #[Groups(['faqTopic:collection:read', 'faqTopic:item:read'])]
    private Collection $faqs;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
        $this->faqs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getProgramChannels(): Collection
    {
        return $this->programChannels;
    }

    public function addProgramChannel(programChannel $programChannel): self
    {
        if (!$this->programChannels->contains($programChannel)) {
            $this->programChannels[] = $programChannel;
        }

        return $this;
    }

    public function removeProgramChannel(programChannel $programChannel): self
    {
        $this->programChannels->removeElement($programChannel);

        return $this;
    }

    public function getFaqs(): Collection
    {
        return $this->faqs;
    }

    public function setFaqs(Collection $faqs): self
    {
        $this->faqs = $faqs;

        return $this;
    }

    public function addFaq(Faq $faq): self
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs[] = $faq;
        }

        return $this;
    }

    public function removeFaq(Faq $faq): self
    {
        $this->faqs->removeElement($faq);

        return $this;
    }
}
