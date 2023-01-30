<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\Ranking;

use App\Entity\Admissibility\Ranking\Coefficient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coefficient>
 *
 * @method Coefficient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coefficient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coefficient[]    findAll()
 * @method Coefficient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoefficientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coefficient::class);
    }

    public function getCoefficientParams(?array $programChannels = []): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select('c.type', 'c.coefficient', 'program_channel.name', 'program_channel.position as program_channel_position', 'program_channel.key as program_channel_key')
            ->join('c.programChannel', 'program_channel')
            ->orderBy('program_channel.position', 'ASC')
        ;

        if (count($programChannels) > 0) {
            $ids = array_map(function($p) {
                return $p->getId();
            }, $programChannels);
            $qb->where($qb->expr()->in('program_channel.id', $ids));
        }

        return array_map(function($item) { 
            $item['position_key'] = sprintf('p%04d', $item['program_channel_position']);
            return $item; 
        }, $qb->getQuery()->getArrayResult());
    }
}
