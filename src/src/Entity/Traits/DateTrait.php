<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTime;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait DateTrait
{
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime', nullable: false, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    #[Groups([
        'notification:collection:read',
        'order:collection:read',
    ])]
    private DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: false, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    #[Groups([
        'order:collection:read',
    ])]
    private DateTime $updatedAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
