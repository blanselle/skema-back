<?php

declare(strict_types=1);

namespace App\Entity\Faq;

use App\Entity\Traits\DateTrait;
use App\Repository\Faq\FaqRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FaqRepository::class)]
class Faq
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text')]
    #[Groups(['faqTopic:collection:read', 'faqTopic:item:read'])]
    private string $question;

    #[ORM\Column(type: 'text')]
    #[Groups(['faqTopic:collection:read', 'faqTopic:item:read'])]
    private string $answer;

    #[ORM\ManyToMany(targetEntity: FaqTopic::class, inversedBy: 'faqs')]
    #[Assert\Count(min: 1, minMessage: 'The Faq must have at least one topic')]
    private Collection $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
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

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function setTopics(Collection $topics): self
    {
        $this->topics = $topics;

        return $this;
    }

    public function addTopic(FaqTopic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
        }

        return $this;
    }

    public function removeTopic(FaqTopic $topic): self
    {
        $this->topics->removeElement($topic);

        return $this;
    }
}
