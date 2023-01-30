<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Entity\OralTest\SudokuConfiguration;
use App\Entity\ProgramChannel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * @method ProgramChannel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgramChannel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgramChannel[]    findAll()
 * @method ProgramChannel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramChannelRepository extends SortableRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(ProgramChannel::class));
    }

    public function getRemainingSudokuProgramChannelsQuery(): QueryBuilder
    {
        $subQueryIn = $this->_em->getRepository(CampusOralDayConfiguration::class)->createQueryBuilder('campus_oral_day_configuration');
        $subQueryIn
            ->join('campus_oral_day_configuration.campus', 'campus')
            ->join('campus_oral_day_configuration.programChannels', 'program_channels')
            ->select('program_channels.id')
            ->distinct()
        ;

        $subQueryOut = $this->_em->getRepository(SudokuConfiguration::class)
            ->createQueryBuilder('c')
            ->join('c.programChannels', 'program_channels')
            ->select('program_channels.id')
            ->distinct()
        ;

        $subQueryOutResult = $subQueryOut->getQuery()->getArrayResult();

        $qb = $this->createQueryBuilder('pc');
        $qb
            ->where($qb->expr()->in('pc.id', ':ids_in'))
            ->setParameter('ids_in', array_map(fn(array $data) => $data['id'], $subQueryIn->getQuery()->getArrayResult()))
            ->orderBy('pc.name', 'ASC')
        ;

        if (count($subQueryOutResult) > 1) {
            $qb
                ->andWhere($qb->expr()->notIn('pc.id', ':ids_out'))
                ->setParameter('ids_out', array_map(fn(array $data) => $data['id'], $subQueryOut->getQuery()->getArrayResult()))
            ;
        }

        return $qb;
    }

    /**
     * @return ProgramChannel[]
     */
    public function findRemainingSudokuProgramChannels(): array
    {
        return $this->getRemainingSudokuProgramChannelsQuery()->getQuery()->getResult();
    }
}
