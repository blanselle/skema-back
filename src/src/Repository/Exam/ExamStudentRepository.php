<?php

declare(strict_types=1);

namespace App\Repository\Exam;

use App\Constants\DatatableConstants;
use App\Constants\Exam\ExamConditionConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Entity\Student;
use App\Entity\User;
use App\Exception\ExamStudent\CollisionException;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @extends ServiceEntityRepository<ExamStudent>
 *
 * @method ExamStudent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamStudent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamStudent[]    findAll()
 * @method ExamStudent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamStudent::class);
    }

    public function getExamStudentsForActiveSessions(array $data): array
    {
        $qb =  $this->createQueryBuilder('s')
            ->join('s.examSession', 'e')
            ->join('s.student', 'st')
            ->join('e.examClassification', 'cl')
            ->andWhere('e.dateStart >= CURRENT_DATE()')
        ;
        if (isset($data['identifier']) && null != $data['identifier']) {
            $qb->andWhere($qb->expr()->like('st.identifier', ':identifier'))
                ->setParameter('identifier', '%'.$data['identifier'].'%')
            ;
        }
        if (isset($data['exam']) && null != $data['exam']) {
            $qb->andWhere($qb->expr()->like('cl.name', ':name'))
                ->setParameter('name', '%'.$data['exam'].'%')
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params = [],
        int $limit = DatatableConstants::TABLE_PAGINATION_LENGTH,
        int $offset = DatatableConstants::TABLE_PAGINATION_START,
        ?array $orders = []
    ): QueryBuilder {
        $query->select('a');
        $query->join('a.examSession', 'e')
            ->join('a.student', 'st')
            ->join('st.user', 'u')
            ->join('e.examClassification', 'cl')
            ->leftJoin('e.campus', 'c')
        ;

        $filters = $params['filters'];

        $query->andWhere($query->expr()->eq('e.type', ':type'))
            ->setParameter('type', ExamSessionTypeConstants::TYPE_INSIDE);

        if (!empty($filters['candidate'])) {
            $query->andWhere('st.identifier LIKE :candidate')
                ->setParameter('candidate', sprintf('%s', '%'.$filters['candidate'].'%'));
        }
        if (!empty($filters['name'])) {
            $query->andWhere('LOWER(st.name) like :name')
                ->setParameter('name', sprintf('%s', '%'.strtolower($filters['name']).'%'));
        }
        if (!empty($filters['exam'])) {
            $query->andWhere('cl.id = :exam')
                ->setParameter('exam', sprintf('%d', $filters['exam']));
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
                'a.createdAt' => 'DESC',
            ];
        }
        foreach ($orders as $key => $order) {
            $query->orderBy($key, $order);
        }

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query;
    }

    public function getExamStudentsByExamSession(ExamSession $examSession, bool $thirdTime = false, bool $allStudent = false ): array
    {
        $query = $this->createQueryBuilder('a')
            ->addSelect('s', 'u')
            ->join('a.student', 's')
            ->join('s.administrativeRecord', 'ar')
            ->join('s.user', 'u', 'WITH', 'u.student = s.id')
            ->andWhere('a.examSession = :examSession')
            ->setParameter('examSession', $examSession)
        ;
            
        if(false === $allStudent)
        {    
            $query
                ->andWhere('ar.thirdTime = :thirdTime')
                ->setParameter('thirdTime', $thirdTime)
            ;
        }
        
        if (true === $thirdTime) {
            $query
                ->andWhere('ar.thirdTimeNeedDetail = :thirdTimeNeedDetail')
                ->setParameter('thirdTimeNeedDetail', true)
            ;
        }

        $query
            ->orderBy('ar.thirdTime', 'DESC')
            ->addOrderBy('u.lastName', 'ASC')
           ;

        return $query->getQuery()->getResult();
    }

    public function getExamStudentsInternByExamClassification(ExamClassification $examClassification): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->join('a.examSession', 'es')
            ->andWhere($qb->expr()->eq('es.type', ':type'))
            ->andWhere($qb->expr()->eq('es.examClassification',  ':examClassification'))
            ->setParameter('examClassification', $examClassification)
            ->setParameter('type', ExamSessionTypeConstants::TYPE_INSIDE)
        ;

        return $qb->getQuery()->getResult();
    }
    public function updateExamStudentRoom(int $sessionId): void
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            UPDATE exam_student SET exam_room_id = NULL WHERE exam_session_id = :exam_session_id
        ");

        $query->executeQuery(['exam_session_id' => $sessionId]);
        
    }
    public function getAvailableRoomByIdWithMinStudents(int $sessionId, bool $thirdTime = false): int|bool
    {
        $connection = $this->_em->getConnection();
        $subquery = "SELECT COUNT(*) FROM exam_student st WHERE st.exam_room_id = r.id";
        $subcondition = (true === $thirdTime) ? " AND r.third_time IS TRUE" : " AND r.third_time IS FALSE";
        $query = $connection->prepare("
            SELECT  
              r.id,
              ($subquery) as nb
            FROM 
                exam_session s
            INNER JOIN exam_session_exam_room re ON re.exam_session_id = s.id
            INNER JOIN exam_room r ON r.id = re.exam_room_id
            WHERE 
                s.id = :sessionId
                $subcondition
                and ($subquery) < r.number_of_places
            GROUP BY r.id
            ORDER BY r.id ASC
            LIMIT 1
		");
        $result = $query->executeQuery(['sessionId' => $sessionId]);
        return $result->fetchOne();
    }

    public function updateExamStudent(int $sessionId, int $studentId, ?int $examRoomId, bool $specific): void
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            UPDATE exam_student SET exam_room_id = :exam_room_id, specific = :specific WHERE exam_session_id = :exam_session_id AND student_id = :student_id
        ");
        $query->executeQuery(['exam_session_id' => $sessionId, 'exam_room_id' => $examRoomId, 'specific' => (int)$specific, 'student_id' => $studentId]);
    }

    public function findByStudentAvoidingCollisionOnOtherCampus(Student $student, ExamSession $examSession): array
    {
        $qb = $this->createQueryBuilder('es');
        return $qb
            ->join('es.examSession', 'ese')
            ->join('ese.campus', 'c')
            ->where($qb->expr()->eq('es.student', ':student'))
            ->andWhere($qb->expr()->neq('c', ':campus'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gte('ese.dateEnd', ':dateEnd'),
                    $qb->expr()->lte('ese.dateStart', ':dateEnd')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('ese.dateStart', ':dateStart'),
                    $qb->expr()->gte('ese.dateEnd', ':dateStart')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('ese.dateStart', ':dateStart'),
                    $qb->expr()->gte('ese.dateEnd', ':dateEnd')
                )
            ))
            ->setParameter('student', $student)
            ->setParameter('campus', $examSession->getCampus())
            ->setParameter('dateEnd', $examSession->getDateStart())
            ->setParameter('dateStart', $examSession->getDateEnd())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByStudentAvoidingCollisionOnSameSessionType(Student $student, ExamSession $examSession): array
    {
        $qb = $this->createQueryBuilder('es');
        return $qb
            ->join('es.examSession', 'ese')
            ->join('ese.campus', 'c')
            ->join('ese.examClassification', 'ec')
            ->where($qb->expr()->eq('es.student', ':student'))
            ->andWhere($qb->expr()->eq('c', ':campus'))
            ->andWhere($qb->expr()->eq('ec.examSessionType', ':examSessionType'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gte('ese.dateEnd', ':dateEnd'),
                    $qb->expr()->lte('ese.dateStart', ':dateEnd')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('ese.dateStart', ':dateStart'),
                    $qb->expr()->gte('ese.dateEnd', ':dateStart')
                ),
                $qb->expr()->andX(
                    $qb->expr()->lte('ese.dateStart', ':dateStart'),
                    $qb->expr()->gte('ese.dateEnd', ':dateEnd')
                )
            ))
            ->setParameter('student', $student)
            ->setParameter('campus', $examSession->getCampus())
            ->setParameter('dateEnd', $examSession->getDateStart())
            ->setParameter('dateStart', $examSession->getDateEnd())
            ->setParameter('examSessionType', $examSession->getExamClassification()->getExamSessionType())
            ->getQuery()
            ->getResult()
        ;
    }

    public function getExamStudentsByExamSessions(ExamSession $examSession): array
    {
        $query = $this->createQueryBuilder('a')
            ->addSelect('CASE WHEN a.score IS NULL THEN 1 ELSE 0 END as HIDDEN list_score_is_null')
            ->join('a.student', 's')
            ->join('s.user', 'u')
            ->andWhere('a.examSession = :examSession')
            ->setParameter('examSession', $examSession)
            ->addOrderBy('list_score_is_null', 'DESC')
            ->addOrderBy('u.lastName', 'ASC')
        ;

        return $query->getQuery()->getResult();
    }

    public function findExamStudentWithIdentityByExamClassification(
        ExamClassification $examClassification,
        string $lastName,
        string $firstName,
        DateTimeInterface $date,
        ?DateTimeInterface $birth = null,
    ): ?ExamStudent {
        $qb = $this->createQueryBuilder('e');
        $qb
            ->leftJoin(Student::class, 's', Expr\Join::WITH, $qb->expr()->eq('e.student', 's'))
            ->leftJoin(User::class, 'u', Expr\Join::WITH, $qb->expr()->eq('u.student', 's'))
            ->leftJoin(ExamSession::class, 'es', Expr\Join::WITH, $qb->expr()->eq('e.examSession', 'es'))
            ->andWhere($qb->expr()->eq('es.examClassification', ':examClassification'))
            ->andWhere($qb->expr()->eq($qb->expr()->upper('UNACCENT(u.firstName)'), $qb->expr()->upper('UNACCENT(:firstName)')))
            ->andWhere($qb->expr()->eq($qb->expr()->upper('UNACCENT(u.lastName)'), $qb->expr()->upper('UNACCENT(:lastName)')))
            ->andWhere($qb->expr()->eq('DATE(es.dateStart)', ':_date'))
            ->setParameters([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'examClassification' => $examClassification,
                '_date' => $date->format('Y-m-d')
            ])

        ;
        
        if (isset($birth)) {
            $qb
                ->andWhere('DATE(s.dateOfBirth) = :birth')
                ->setParameter('birth', $birth->format('Y-m-d'))
            ;
        }

        $result = $qb->getQuery()->getResult();
        if (count($result) > 1) {
            throw new CollisionException(sprintf("Plus d'une session a été trouvée pour ce candidat: %s %s", $lastName, $firstName));
        }

        return $result[0]?? null;
    }

    public function findExamStudentWithIdentityBySession(ExamSession $examSession, string $lastName, string $firstName, ?DateTime $birth = null): array
    {
        $query = $this->createQueryBuilder('st');
        $query
            ->join('st.student', 's')
            ->join('s.user', 'u', 'WITH', 'u.student = st.student')
            ->andWhere('st.examSession = :examSession')
            ->setParameter('examSession', $examSession)
            ->andWhere('upper(UNACCENT(u.firstName)) = upper(UNACCENT(:firstName))')
            ->setParameter('firstName', $firstName)
            ->andWhere('upper(UNACCENT(u.lastName)) = upper(UNACCENT(:lastName))')
            ->setParameter('lastName', $lastName)
        ;
        if (isset($birth)) {
            $query->andWhere('DATE(s.dateOfBirth) = :birth')
                ->setParameter('birth', $birth->format('Y-m-d'))
            ;
        }
        return $query->getQuery()->getResult();
    }

    public function countNbStudentByExamClassificationAndScore(ExamClassification $examClassification, float $score): int|bool
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            SELECT  
              COUNT(es.id)
            FROM 
                exam_student es
            INNER JOIN exam_session e ON e.id = es.exam_session_id
            WHERE 
                e.exam_classification_id = :examClassificationId
                AND es.score = :score
            GROUP BY es.score
            LIMIT 1
		");
        $result = $query->executeQuery(['examClassificationId' => $examClassification->getId(), 'score' => $score]);

        return $result->fetchOne();
    }

    public function getBestAdmissibilityNoteByType(int $id, string $type): ?ExamStudent
    {
        $qb = $this->createQueryBuilder('exam_student');
        $qb
            ->join('exam_student.examSession', 'exam_session')
            ->join('exam_session.examClassification', 'exam_classification')
            ->join('exam_classification.examSessionType', 'exam_session_type')
            ->where($qb->expr()->eq('exam_student.student', ':_student'))
            ->andWhere($qb->expr()->eq('exam_session_type.name', ':_name'))
            ->setParameters([
                '_student' => $id,
                '_name' => $type
            ])
            ->orderBy('exam_student.admissibilityNote', 'DESC')
            ->setMaxResults(1)
        ;

        $result = $qb->getQuery()->getResult();
        $exam = null;
        if (count($result) > 0) {
            $exam = $result[0];
        }

        return $exam;
    }

    public function fetchExamStudentsForHandler(int $id): array
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            SELECT 
                exam_student.id,
                exam_student.score,
                exam_classification.id as exam_classification_id, 
                exam_classification.name as exam_classification_name
            FROM 
                exam_student as exam_student
            INNER JOIN exam_session as exam_session ON exam_session.id = exam_student.exam_session_id
            INNER JOIN exam_classification as exam_classification ON exam_classification.id = exam_session.exam_classification_id
            WHERE exam_student.student_id = :_student_id
		");

        $result = $query->executeQuery(['_student_id' => $id]);

        return $result->fetchAllAssociative();
    }

    public function updateAdmissibilityNote(int $id, float $note): void
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            UPDATE exam_student SET admissibility_note = :_admissibility_note WHERE exam_student.id = :_id
        ");
        $query->executeQuery(['_admissibility_note' => $note, '_id' => $id]);
    }
}
