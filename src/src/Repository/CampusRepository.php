<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants\Exam\ExamConditionConstants;
use App\Entity\Campus;
use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Entity\ProgramChannel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    public function getExamSessionsActiveByCampus(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.examSessions', 's', 'WITH', 'c.id = s.campus')
            ->join('s.examClassification', 'cl')
            ->andWhere('s.dateStart >= CURRENT_DATE()')
            ->andWhere('cl.name != :condition')
            ->setParameter('condition', ExamConditionConstants::CONDITION_ONLINE)
            ->orderBy('c.name', 'ASC')
            ->getQuery()->getResult()
        ;
    }

    public function getContestJuryWebsiteCodes(): array
    {
        $codes = $this->createQueryBuilder('c')
            ->select('c.contestJuryWebsiteCode')
            ->distinct(true)
            ->orderBy('c.contestJuryWebsiteCode', 'ASC')
            ->getQuery()->getArrayResult();

        return array_map(function($code) {
            return $code['contestJuryWebsiteCode'];
        }, $codes);
    }

    /**
     * @param ProgramChannel[] $programChannels
     * @return Campus[]
     */
    public function getOralTestCampusesWithCapacity(array $programChannels): array
    {
        $subQuery = $this->_em->getRepository(CampusOralDayConfiguration::class)->createQueryBuilder('campus_oral_day_configuration');
        $subQuery
            ->join('campus_oral_day_configuration.campus', 'campus')
            ->join('campus_oral_day_configuration.programChannels', 'program_channels')
            ->where($subQuery->expr()->in('program_channels.id', ':pc_ids'))
            ->setParameter('pc_ids', array_map(fn(ProgramChannel $programChannel) => $programChannel->getId(), $programChannels))
            ->select('campus.id')
            ->distinct()
            ;

        $qb = $this->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter('ids', array_map(fn(array $data) => $data['id'], $subQuery->getQuery()->getArrayResult()))
            ->orderBy('c.assignmentCampus', 'DESC')
            ->addOrderBy('c.name', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }
}
