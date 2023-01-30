<?php

declare(strict_types=1);

namespace App\Service\Cv;

use App\Entity\CV\BacSup;
use App\Repository\CV\BacSupRepository;
use Doctrine\ORM\EntityManagerInterface;

class BacSupLevel
{
    public function __construct(private EntityManagerInterface $em, private BacSupRepository $bacSupRepository) {}

    public function get(BacSup $bacSup, array $bacSups = null): int
    {
        if(null === $bacSups) {
            $bacSups = $this->bacSupRepository->findBy(['cv' => $bacSup->getCv()], ['id' => 'asc']);
        }

        // INFO: Le numéro d'un bacSup c'est sa position +1 (car un tableau commence par 0 et non par 1)

        if(!$this->em->contains($bacSup)) {
            return count($bacSups)+1;
        }

        $levels = [];
        $i = 0;
        foreach($bacSups as $b) {
            if (null === $b->getParent()) {
                $levels[$b->getId()] = $i + 1;
                $i++;
            }
        }

        // if dual get level of parent
        return (null === $bacSup->getParent())? $levels[$bacSup->getId()] : $levels[$bacSup->getParent()->getId()];
    }
}
