<?php

declare(strict_types=1);

namespace App\Interface;

use App\Entity\Admissibility\Bonus\Category;
use App\Entity\ProgramChannel;

interface BonusInterface
{
    public function getId(): int;

    public function setId(int $id): self;

    public function getValue(): float;

    public function setValue(float $value): self;

    public function getProgramChannel(): ProgramChannel;

    public function setProgramChannel(ProgramChannel $programChannel): self;

    public function getCategory(): Category;

    public function setCategory(Category $category): self;

    /**
     * Retourne les information supplémentaire (pour le crud)
     *
     * @return array
     */
    public function getInfos(): array;

    public function getClass(): string;
}
