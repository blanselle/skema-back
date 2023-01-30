<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\LanguageBonus;
use App\Entity\Traits\BonusRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LanguageBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LanguageBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LanguageBonus[]    findAll()
 * @method LanguageBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageBonusRepository extends ServiceEntityRepository
{

    use BonusRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LanguageBonus::class);
    }
}
