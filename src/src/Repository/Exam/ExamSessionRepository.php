<?php

declare(strict_types=1);

namespace App\Repository\Exam;

use App\Constants\Exam\ExamConditionConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Exam\ExamSession;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExamSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamSession[]    findAll()
 * @method ExamSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamSession::class);
    }

    public function getExamSessionsOnline(bool $active = true, ?ExamSession $examSession = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->join('e.examClassification', 'cl')
            ->join('cl.examCondition', 'c');
        if (true === $active) {
            $qb->andWhere('e.dateStart >= CURRENT_DATE()');
        }
        $qb
            ->andWhere('c.name = :condition')
            ->setParameter('condition', ExamConditionConstants::CONDITION_ONLINE)
        ;
        if (!empty($examSession)) {
            $qb->andWhere('e.id = :id')
                ->setParameter('id', $examSession->getId())
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function getExamSessionsNameActive(): array
    {
        return $this->createQueryBuilder('e')
            ->select('cl.name')
            ->join('e.examClassification', 'cl')
            ->join('cl.examCondition', 'c')
            ->andWhere('e.dateStart >= CURRENT_DATE()')
            ->groupBy('cl.name')
            ->orderBy('cl.name', 'ASC')
            ->getQuery()->getResult()
        ;
    }

    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params,
        int $limit,
        int $offset = null,
        ?array $orders = []
    ): QueryBuilder {
        $query
            ->join('a.examClassification', 'cl')
            ->leftJoin('a.campus', 'c')
        ;

        $filters = $params['filters'];
        $query->andWhere($query->expr()->eq('a.type', ':type'))
            ->setParameter('type', ExamSessionTypeConstants::TYPE_INSIDE);

        if (isset($filters['name']) && null != $filters['name']) {
            $query->andWhere($query->expr()->like('LOWER(cl.name)', ':name'))
                ->setParameter('name', sprintf('%s', '%'.strtolower($filters['name']).'%'))
            ;
        }

        if (!empty($filters['campus']) && 'online' !== $filters['campus']) {
            $query->andWhere('c.id = :campus')
                ->setParameter('campus', sprintf('%d', $filters['campus']));
        }

        if (!empty($filters['campus']) && 'online' === $filters['campus']) {
            $query
                ->join('cl.examCondition', 'ec')
                ->andWhere('ec.name = :condition')
                ->setParameter('condition', ExamConditionConstants::CONDITION_ONLINE)
            ;
        }

        if (empty($orders)) {
            $orders = [
                'a.dateStart' => 'DESC',
            ];
        }
        foreach ($orders as $key => $order) {
            $query->orderBy($key, $order);
        }

        if (empty($offset)) {
            $offset = 0;
        }
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query;
    }

    public function getCampusesWithExamSessionDistributed(): array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->select('c.id')
            ->join('a.campus', 'c')
            ->andWhere($qb->expr()->eq('a.distributed', ':distributed'))
            ->setParameter('distributed', true)
            ->groupBy('c.id')
            ->getQuery()->getSingleColumnResult()
        ;
    }

    public function getNbTotalPlaces(int $sessionId, bool $thirdTime = false): int
    {
        $connection = $this->_em->getConnection();
        $subcondition = (true === $thirdTime) ? " AND r.third_time IS TRUE" : "";
        $query = $connection->prepare("
            SELECT SUM(r.number_of_places) 
            FROM 
                exam_session s
            INNER JOIN exam_session_exam_room re ON re.exam_session_id = s.id
            INNER JOIN exam_room r ON r.id = re.exam_room_id
            WHERE
                s.id = :sessionId
                {$subcondition}
        ");
        $result = $query->executeQuery(['sessionId' => $sessionId]);
        return (int)$result->fetchOne();
    }

    public function getExamSessionsByStudent(Student $student): array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->join('a.examStudents', 'es')
            ->andWhere('es.student = :student')
            ->setParameter('student', $student)
            ->getQuery()->getResult()
        ;
    }
}
