<?php

namespace App\Repository\OralTest;

use App\Entity\Campus;
use App\Entity\Exam\ExamLanguage;
use App\Entity\OralTest\CampusOralDay;
use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Entity\ProgramChannel;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampusOralDay>
 *
 * @method CampusOralDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampusOralDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampusOralDay[]    findAll()
 * @method CampusOralDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusOralDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampusOralDay::class);
    }

    public function save(CampusOralDay $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CampusOralDay $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByCampusDayFields(CampusOralDayConfiguration $configuration, DateTimeImmutable $date, ?ExamLanguage $first, ?ExamLanguage $second): ?CampusOralDay
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->join('c.configuration', 'configuration')
            ->where($qb->expr()->eq('c.configuration', ':configuration'))
            ->setParameter('configuration', $configuration)
            ->andWhere($qb->expr()->eq('c.date', ':date'))
            ->setParameter('date', $date)
        ;

        if (null !== $first) {
            $qb->andWhere($qb->expr()->eq('c.firstLanguage', ':first'))
                ->setParameter('first', $first)
            ;
        } else {
            $qb->andWhere($qb->expr()->isNull('c.firstLanguage'));
        }

        if (null !== $second) {
            $qb->andWhere($qb->expr()->eq('c.secondLanguage', ':second'))
                ->setParameter('second', $second)
            ;
        } else {
            $qb->andWhere($qb->expr()->isNull('c.secondLanguage'));
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Campus[]
     */
    public function findCampusesAvailable(ProgramChannel $programChannel, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null): array
    {
        $qb = $this->createQueryBuilderForAvailableSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
        $qb
            ->select('ca.id')
            ->distinct()
            ->join('co.campus', 'ca')
        ;

        $ids = array_map(fn(array $data) => $data['id'], $qb->getQuery()->getResult());

        $qbCampus = $this->_em->getRepository(Campus::class)->createQueryBuilder('ca');
        $qbCampus
            ->andWhere($qbCampus->expr()->in('ca.id', ':_campuses'))
            ->setParameter('_campuses', $ids)
        ;

        return $qbCampus->getQuery()->getResult();
    }

    /**
     * @return Campus[]
     */
    public function getCampusesWithCapacity(ProgramChannel $programChannel, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null): array
    {
        $qb = $this->createQueryBuilderForAllSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
        $qb
            ->select('ca.id')
            ->distinct()
            ->join('co.campus', 'ca')
        ;

        $ids = array_map(fn(array $data) => $data['id'], $qb->getQuery()->getResult());

        $qbCampus = $this->_em->getRepository(Campus::class)->createQueryBuilder('ca');
        $qbCampus
            ->andWhere($qbCampus->expr()->in('ca.id', ':_campuses'))
            ->setParameter('_campuses', $ids)
        ;

        return $qbCampus->getQuery()->getResult();
    }

    /**
     * @return CampusOralDay[]
     */
    public function findByCampusAndProgramChannelAndLanguages(Campus $campus, ProgramChannel $programChannel, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null): array
    {
        $qb = $this->createQueryBuilderForAvailableSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);

        $qb
            ->andWhere($qb->expr()->eq('co.campus', ':_campus'))
            ->setParameter('_campus', $campus)
            ->orderBy('c.date', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return DatetimeInterface[]
     */
    public function findDatesByCampusAndProgramChannelAndLanguages(Campus $campus, ProgramChannel $programChannel, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null): array
    {
        $qb = $this->createQueryBuilderForAvailableSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);

        $qb
            ->select('c.date')
            ->distinct()
            ->andWhere($qb->expr()->eq('co.campus', ':_campus'))
            ->setParameter('_campus', $campus)
            ->orderBy('c.date', 'ASC')
        ;

        $result = $qb->getQuery()->getResult();

        return array_map(fn(array $date): DatetimeInterface => $date['date'], $result);
    }

    /**
     * @return DatetimeInterface[]
     */
    public function findAllDatesByCampusAndProgramChannelAndLanguages(Campus $campus, ProgramChannel $programChannel, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null): array
    {
        $qb = $this->createQueryBuilderForAllSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);

        $qb
            ->select('c.date')
            ->distinct()
            ->andWhere($qb->expr()->eq('co.campus', ':_campus'))
            ->setParameter('_campus', $campus)
            ->orderBy('c.date', 'ASC')
        ;

        $result = $qb->getQuery()->getResult();

        return array_map(fn(array $date): DatetimeInterface => $date['date'], $result);
    }

    /**
     * @return CampusOralDay[]
     */
    public function findAvailableSlotByDateAndCampusAndProgramChannelAndLanguages(Campus $campus, ProgramChannel $programChannel, DateTimeInterface $start, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null): array
    {
        $qb = $this->createQueryBuilderForAvailableSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
        $qb
            ->andWhere($qb->expr()->eq('co.campus', ':_campus'))
            ->setParameter('_campus', $campus)
            ->andWhere($qb->expr()->gte('c.date', ':_start_date'))
            ->setParameter('_start_date', $start)
            ->orderBy('c.date', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findOneSlot(Campus $campus, ProgramChannel $programChannel, DateTimeInterface $start, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null): ?CampusOralDay
    {
        $qb = $this->createQueryBuilderForAllSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
        $qb
            ->andWhere($qb->expr()->eq('co.campus', ':_campus'))
            ->setParameter('_campus', $campus)
            ->andWhere($qb->expr()->eq('c.date', ':_date'))
            ->setParameter('_date', $start)
            ->orderBy('c.date', 'ASC')
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return CampusOralDay[]
     */
    public function findCampusToBeRemovedByConfiguration(CampusOralDayConfiguration $configuration): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->eq('c.configuration', ':_configuration'))
            ->setParameter('_configuration', $configuration->getId())
            ->andWhere($qb->expr()->eq('c.nbOfReservedPlaces', ':_value'))
            ->setParameter('_value', 0)
        ;

        if ($configuration->getFirstLanguages()->count() > 0) {
            $qb
                ->andWhere($qb->expr()->notIn('c.firstLanguage', ':_first_language_ids'))
                ->setParameter('_first_language_ids', array_map(fn(ExamLanguage $language) => $language->getId(), $configuration->getFirstLanguages()->toArray()))
            ;
        } else {
            $qb->andWhere($qb->expr()->isNull('c.firstLanguage'));
        }

        if ($configuration->getSecondLanguages()->count() > 0) {
            $qb
                ->andWhere($qb->expr()->notIn('c.secondLanguage', ':_second_language_ids'))
                ->setParameter('_second_language_ids', array_map(fn(ExamLanguage $language) => $language->getId(), $configuration->getSecondLanguages()->toArray()))
            ;
        } else {
            $qb->andWhere($qb->expr()->isNull('c.secondLanguage'));
        }

        return $qb->getQuery()->getResult();
    }

    public function getNumberOfReservedPlaces(CampusOralDayConfiguration $configuration, ?int $firstLanguageId = null, ?int $secondLanguageId = null): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select('SUM(c.nbOfReservedPlaces) as total')
            ->where($qb->expr()->eq('c.configuration', ':_configuration'))
            ->setParameter('_configuration', $configuration->getId())
        ;

        if (null !== $firstLanguageId) {
            $qb
                ->andWhere($qb->expr()->eq('c.firstLanguage', ':firstLanguage'))
                ->setParameter('firstLanguage', $firstLanguageId)
            ;
        }

        if (null !== $secondLanguageId) {
            $qb
                ->andWhere($qb->expr()->eq('c.secondLanguage', ':secondLanguage'))
                ->setParameter('secondLanguage', $secondLanguageId)
            ;
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function createQueryBuilderForAvailableSlots(ProgramChannel $programChannel, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null, ?QueryBuilder $qb = null): QueryBuilder
    {
        $qb = $this->createQueryBuilderForAllSlots(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
        
        $qb
            ->andWhere($qb->expr()->gt($qb->expr()->diff('c.nbOfAvailablePlaces', 'c.nbOfReservedPlaces'), ':_min'))
            ->setParameter('_min', 0)
        ;

        return $qb;
    }

    private function createQueryBuilderForAllSlots(ProgramChannel $programChannel, ExamLanguage $firstLanguage, ?ExamLanguage $secondLanguage = null, ?QueryBuilder $qb = null): QueryBuilder
    {
        if (null === $qb) {
            $qb = $this->createQueryBuilder('c');
        }
        $qb
            ->join('c.configuration', 'co')
            ->join('co.programChannels', 'pc')
            ->andWhere($qb->expr()->in('pc.id', ':_program_channels'))
            ->setParameter('_program_channels', [$programChannel->getId()])
            ->andWhere($qb->expr()->eq('c.firstLanguage', ':_first_language'))
            ->setParameter('_first_language', $firstLanguage->getId())
        ;

        if (null === $secondLanguage) {
            $qb->andWhere($qb->expr()->isNull('c.secondLanguage'));
        } else {
            $qb
                ->andWhere($qb->expr()->eq('c.secondLanguage', ':_second_language'))
                ->setParameter('_second_language', $secondLanguage->getId())
            ;
        }

        return $qb;
    }
}
