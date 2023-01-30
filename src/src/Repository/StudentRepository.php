<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants\Exam\ExamSessionTypeCodeConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Constants\Exam\ExamSessionTypeNameConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaStateSimplifyConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\SchoolReport;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamStudent;
use App\Entity\Media;
use App\Entity\Payment\Order;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Model\Student\ExportStudentListModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * @param array $fields
     * @param array $list
     * @param array $result
     * @return array
     */
    private static function updateResult(array $fields, array $list, array $result): array
    {
        return array_map(function ($student) use ($fields, $list) {
            $r = $list[$student['student_id']] ?? null;

            foreach ($fields as $field) {
                $alias = str_replace('.', '_', $field);
                $student[$alias] = $r[$alias] ?? null;
            }

            return $student;
        }, $result);
    }

    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params,
        int $limit = null,
        int $offset = null,
        ?array $orders = [],
    ): QueryBuilder {
        $query->join('a.user', 'u')
            ->join('a.programChannel', 'p')
        ;

        $filters = $params['filters'];
        if (isset($filters['identifier']) && null != $filters['identifier']) {
            $query->andWhere($query->expr()->like('a.identifier', ':identifier'))
                ->setParameter('identifier', sprintf('%s', '%'.$filters['identifier'].'%'))
            ;
        }
        if (isset($filters['lastname']) && null != $filters['lastname']) {
            $query
                ->andWhere('upper(UNACCENT(u.lastName)) like upper(UNACCENT(:query))')
                ->setParameter('query', '%'.$filters['lastname'].'%')
            ;
        }
        if (isset($filters['state']) && null != $filters['state']) {
            $query->andWhere('a.state = :state')
                ->setParameter('state', sprintf('%s', (string)$filters['state']))
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

        if (empty($offset)) {
            $offset = 0;
        }

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query;
    }

    public function findSessionByIdInArray(int $sessionId): array
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            SELECT 
                u.first_name as firstName,
                u.last_name as lastName,
                r.name as room,
                s.identifier as identifier
            FROM 
                exam_student e,
                student s,
                users u,
                exam_room r
            WHERE 
                e.exam_session_id = :sessionId
            AND e.student_id = s.id
            AND u.student_id = s.id
            AND e.exam_room_id = r.id
            ORDER BY r.third_time DESC, exam_room_id ASC, lastName ASC
		");

        $result = $query->executeQuery(['sessionId' => $sessionId]);

        return $result->fetchAllAssociative();
    }

    public function findNbInscriptionByState(ProgramChannel $programChannel): array
    {
        $qb = $this->createQueryBuilder('s');
        $result = $qb
            ->select('s.state as state, count(s.id) as count')
            ->leftJoin('s.programChannel', 'p')
            ->andWhere($qb->expr()->eq('p', ':programChannel'))
            ->groupBy('s.state')
            ->setParameter('programChannel', $programChannel)
            ->getQuery()->getResult()
        ;

        return $result;
    }

    public function getValidStudentshipRanking(ProgramChannel $programChannel, array $sort = []): array
    {
        $qb = $this->createQueryBuilder('student');
        $qb
            ->join('student.examStudents', 'exam_students')
            ->join('exam_students.examSession', 'exam_session')
            ->join('exam_session.examClassification', 'exam_classification')
            ->join('exam_classification.examSessionType', 'exam_session_type')
            ->where('student.programChannel = :_program_channel')
            ->andWhere($qb->expr()->in('student.state', [
                StudentWorkflowStateConstants::STATE_ELIGIBLE,
                StudentWorkflowStateConstants::STATE_COMPLETE,
                StudentWorkflowStateConstants::STATE_APPROVED,
            ]))
            ->setParameter('_program_channel', $programChannel)
        ;

        if (count($sort) > 0) {
            foreach ($sort as $key => $value) {
                $qb->addOrderBy(sprintf('student.%s', $key), $value);
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function getNotesForRankingExport(ProgramChannel $programChannel): array
    {
        $qb = $this->createQueryBuilder('student');
        $qb
            ->select(
                'student.id',
                'student.admissibilityGlobalNote as admissibility_global_note',
                'exam_session_type_english.name as exam_session_type_english_name',
                'exam_classification_english.name as exam_classification_english_name',
                'exam_student_english.admissibilityNote as exam_student_english_note',
                'exam_session_type_management.name as exam_session_type_management_name',
                'exam_classification_management.name as exam_classification_management_name',
                'exam_student_management.admissibilityNote as exam_student_management_note'
            )
            ->leftJoin('student.englishNoteUsed', 'exam_student_english')
            ->join('exam_student_english.examSession', 'exam_session_english')
            ->join('exam_session_english.examClassification', 'exam_classification_english')
            ->join('exam_classification_english.examSessionType', 'exam_session_type_english')
            ->leftJoin('student.managementNoteUsed', 'exam_student_management')
            ->join('exam_student_management.examSession', 'exam_session_management')
            ->join('exam_session_management.examClassification', 'exam_classification_management')
            ->join('exam_classification_management.examSessionType', 'exam_session_type_management')
            ->where('student.programChannel = :_program_channel')
            ->andWhere($qb->expr()->in('student.state', [
                StudentWorkflowStateConstants::STATE_ELIGIBLE,
                StudentWorkflowStateConstants::STATE_COMPLETE,
                StudentWorkflowStateConstants::STATE_APPROVED,
            ]))
            ->orderBy('student.id', 'ASC')
            ->setParameter('_program_channel', $programChannel)
        ;

        $students = $qb->getQuery()->getArrayResult();
        $nbOfCandidates = count($students);

        $notes = []; // data ordering by classification name
        $noteValues = []; // global note to export record
        $classifications = $this->_em->getRepository(ExamClassification::class)->findAll();
        foreach ($classifications as $classification) {
            $notes[$classification->getName()] = ['name' => $classification->getName(), 'sum_of_notes' => 0, 'total_candidates' => 0];
        }

        foreach ($students as $item) {
            $notes[$item['exam_classification_english_name']]['sum_of_notes'] += (float)$item['exam_student_english_note'];
            $notes[$item['exam_classification_english_name']]['total_candidates'] += 1;

            $notes[$item['exam_classification_management_name']]['sum_of_notes'] += (float)$item['exam_student_management_note'];
            $notes[$item['exam_classification_management_name']]['total_candidates'] += 1;

            $noteValues[] = $item['admissibility_global_note'];
        }

        $notes = array_values($notes);

        return ['nbOfCandidates' => $nbOfCandidates, 'notes' => $notes, 'noteValues' => $noteValues];
    }

    public function fetchStudentsForHandler(): array
    {
        $connection = $this->_em->getConnection();
        $sql = "
            SELECT 
                s.id, 
                s.identifier as student_identifier,
                pc.name as program_channel_name,
                pc.id as program_channel_id
            FROM 
                student s
            INNER JOIN program_channel pc ON pc.id = s.program_channel_id
            WHERE s.state IN (?)
		";

        $result = $connection->executeQuery($sql, [[
            StudentWorkflowStateConstants::STATE_ELIGIBLE,
            StudentWorkflowStateConstants::STATE_COMPLETE,
            StudentWorkflowStateConstants::STATE_APPROVED,
        ]], [Connection::PARAM_STR_ARRAY]);

        return $result->fetchAllAssociative();
    }

    public function updateNoteUsed(int $studentId, string $type, int $examStudentId): void
    {
        $connection = $this->_em->getConnection();
        if ($type === ExamSessionTypeNameConstants::TYPE_ENGLISH) {
            $sql = "UPDATE student SET english_note_used_id = :_exam_id WHERE id = :_id";
        } else {
            $sql = "UPDATE student SET management_note_used_id = :_exam_id WHERE id = :_id";
        }

        $query = $connection->prepare($sql);
        $query->executeQuery(['_id' => $studentId,'_exam_id' => $examStudentId]);
    }

    public function getEligibleStudents(ProgramChannel $programChannel, ?float $score = null, ?bool $eligible = true): array
    {
        $qb = $this->createQueryBuilder('student');
        $qb
            ->where($qb->expr()->eq('student.state', ':_state'))
            ->andWhere($qb->expr()->eq('student.programChannel', ':_programChannel'))
            ->setParameters([
                '_state' => StudentWorkflowStateConstants::STATE_APPROVED,
                '_programChannel' => $programChannel,
            ])
            ->orderBy('student.admissibilityGlobalScore', 'ASC')
        ;

        if ($score !== null) {
            if ($eligible) {
                $qb
                    ->andWhere($qb->expr()->gte('student.admissibilityGlobalScore', ':_score'))
                    ->setParameter('_score', $score)
                ;
            } else {
                $qb
                    ->andWhere($qb->expr()->lt('student.admissibilityGlobalScore', ':_score'))
                    ->setParameter('_score', $score)
                ;
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where($qb->expr()->eq('s.id', ':_id'))
            ->setParameter('_id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function fetchStudentsForClosingRegistration(array $states, array $programChannels): array
    {
        $qb = $this->createQueryBuilder('student');
        $qb
            ->andWhere($qb->expr()->in('student.state', ':_states'))
            ->andWhere($qb->expr()->in('student.programChannel', ':_program_channels'))
            ->setParameters([
                '_states' => $states,
                '_program_channels' => array_map(function($p) { return $p->getId(); }, $programChannels)
            ])
        ;

        return $qb->getQuery()->getResult();
    }

    public function exportStudentList(ExportStudentListModel $model): array
    {

        $columns = $model->getColumns();
        if (empty($columns)) {
            throw new BadRequestHttpException('No field to export');
        }

        $columnsFiltered = array_filter($columns, function($field) {
            if ($field->isIntern() && !$field->isManual()) {
                return $field;
            }
        });

        $externalColumnsFiltered = array_filter($columns, function($field) {
            if (!$field->isIntern()) {
                return $field;
            }
        });

        $externalFields = array_map(function($field) {
            return $field->getValue();
        }, $externalColumnsFiltered);

        // Add id to generate url
        $fields = array_map(function($c) {
            return sprintf('%s AS %s', $c->getValue(), str_replace('.', '_', $c->getValue()));
        }, $columnsFiltered);
        $fields = array_merge(['student.id AS student_id'], $fields);

        $qb = $this->createQueryBuilder('student');
        $qb
            ->select(implode(',', $fields))
            ->innerJoin('student.user', 'user')
            ->innerJoin('student.countryBirth', 'countryBirth')
            ->innerJoin('student.nationality', 'nationality')
            ->leftJoin('student.nationalitySecondary', 'nationalitySecondary')
            ->innerJoin('student.country', 'country')
            ->leftJoin('student.administrativeRecord', 'administrativeRecord')
            ->leftJoin('administrativeRecord.scholarShipLevel', 'scholarShipLevel')
            ->leftJoin('administrativeRecord.sportLevel', 'sportLevel')
            ->innerJoin('student.programChannel', 'programChannel')
            ->leftJoin('administrativeRecord.jdc', 'jdc')
            ->leftJoin('student.cv', 'cv')
            ->leftJoin('cv.bac', 'bac')
            ->leftJoin('bac.bacTypes', 'bacTypes')
            ->leftJoin('bac.bacChannel', 'bacChannel')
            ->leftJoin('bac.bacDistinction', 'bacDistinction')
            ->leftJoin('bac.bacOption', 'bacOption')
            ->leftJoin('student.orders', 'schoolRegistrationFees', Expr\Join::WITH, $qb->expr()->eq('schoolRegistrationFees.type', $qb->expr()->literal(OrderTypeConstants::SCHOOL_REGISTRATION_FEES)))
            ->leftJoin('schoolRegistrationFees.payments', 'schoolRegistrationFeesPayment', Expr\Join::WITH, $qb->expr()->eq('schoolRegistrationFeesPayment.state', $qb->expr()->literal(PaymentWorkflowStateConstants::STATE_VALIDATED)))
        ;

        if (null != $model->getIdentifier()) {
            $qb->andWhere($qb->expr()->like('student.identifier', ':identifier'))
                ->setParameter('identifier', sprintf('%s', '%'.$model->getIdentifier().'%'))
            ;
        }
        if (null != $model->getLastname()) {
            $qb
                ->andWhere('upper(UNACCENT(user.lastName)) like upper(UNACCENT(:query))')
                ->setParameter('query', '%'.$model->getLastname().'%')
            ;
        }
        if (null != $model->getState()) {
            $qb->andWhere('student.state = :state')
                ->setParameter('state', sprintf('%s', $model->getState()))
            ;
        }

        $qb = $this->addFilterToSearchStudents(
            qb: $qb,
            alias: 'student',
            intern: $model->isIntern(),
            media: $model->getMedia(),
            mediaCode: $model->getMediaCode()
        );

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();
        try {
            $result = $query->getArrayResult();
        } catch (\Exception $e) {
            return [];
        }

        $studentIds = array_map(function($student) { return $student['student_id']; }, $result);

        $mediaFields = preg_grep('/^media_.*/', $externalFields);
        if (false !== $mediaFields and count($mediaFields) > 0) {
            $this->getMediaToExport(externalColumnsFiltered: $externalColumnsFiltered, result: $result, studentIds: $studentIds);
        }

        if (in_array('idCards', $externalFields, true)) {
            $this->getAdministrativeRecordMediasToExport(collection: 'idCards', result: $result, studentIds: $studentIds);
        }

        if (in_array('studentDiplomas', $externalFields, true)) {
            $this->hasDualCourseOfStudy(result: $result, studentIds: $studentIds);
        }

        $principalDiplomaFields = preg_grep('/^studentDiploma_.*/', $externalFields);
        if (false !== $principalDiplomaFields and count($principalDiplomaFields) > 0) {
            $this->getPrincipalDiplomaToExport(fields: $principalDiplomaFields, result: $result, studentIds: $studentIds);
        }

        $dualDiplomaFields = preg_grep('/^studentDiplomaDual_.*/', $externalFields);
        if (false !== $dualDiplomaFields and count($dualDiplomaFields) > 0) {
            $this->getDualDiplomaToExport(fields: $dualDiplomaFields, result: $result, studentIds: $studentIds);
        }

        $bacSupFields = preg_grep('/^bacSups_.*/', $externalFields);
        if (false !== $bacSupFields and count($bacSupFields) > 0) {
            $this->getBacSupsToExport(fields: $bacSupFields, result: $result, studentIds: $studentIds);
        }

        if (in_array('experiences', $externalFields, true)) {
            $this->getExperiences(result: $result, studentIds: $studentIds);
        }

        if (in_array('languages', $externalFields, true)) {
            $this->getLanguages(result: $result, studentIds: $studentIds);
        }

        $examStudentsANGFields = preg_grep('/^examStudents_ANG_.*/', $externalFields);
        if (false !== $examStudentsANGFields and count($examStudentsANGFields) > 0) {
            $this->getExamStudentToExport(fields: $examStudentsANGFields, result: $result, studentIds: $studentIds);
        }
        $examStudentsMANFields = preg_grep('/^examStudents_MAN_.*/', $externalFields);
        if (false !== $examStudentsMANFields and count($examStudentsMANFields) > 0) {
            $this->getExamStudentToExport(fields: $examStudentsMANFields, result: $result, studentIds: $studentIds, type: ExamSessionTypeCodeConstants::MANAGEMENT);
        }

        return $result;
    }

    public function addFilterToSearchStudents(QueryBuilder $qb, string $alias, bool $intern, ?string $media, ?string $mediaCode, ?bool $externalSession = false): QueryBuilder
    {
        if (empty($media) and empty($mediaCode)) {
            return $qb;
        }

        $criteria = ($intern)? ['intern' => true] : [];
        $programChannels = $this->_em->getRepository(ProgramChannel::class)->findBy($criteria);
        /** @var MediaRepository $mediaRepository */
        $mediaRepository = $this->_em->getRepository(Media::class);

        if (!empty($mediaCode)) {
            if (MediaStateSimplifyConstants::MISSING === $media) {
                $ids = [];

                foreach ($programChannels as $programChannel) {
                    $ids = array_merge($ids, $mediaRepository->findIdsStudentWithMediaMissing(programChannel: $programChannel, mediaCode: $mediaCode, externalSession: $externalSession));
                }

                if (empty($ids)) {
                    $ids = [0]; // WORKAROUND : WHERE s.id in () throw SQL error. So I put 0 (unused id) to get request false
                }

                $qb->andWhere($qb->expr()->in("{$alias}.id", $ids));

                return $qb;
            }

            if (MediaStateSimplifyConstants::TO_VALIDATE === $media) {
                $ids = [];
                foreach ($programChannels as $programChannel) {
                    $ids = array_merge($ids, $mediaRepository->findIdsStudentWithMediaToValidate(programChannel: $programChannel, codeMedia: $mediaCode, externalSession: $externalSession));
                }

                if (empty($ids)) {
                    $ids = [0]; // WORKAROUND : WHERE s.id in () throw SQL error. So I put 0 (unused id) to get request false
                }

                $qb->andWhere($qb->expr()->in("{$alias}.id", $ids));

                return $qb;
            }

            if (empty($media)) {
                $ids = [];
                foreach ($programChannels as $programChannel) {
                    $ids = array_merge($ids, $mediaRepository->findIdsStudentWithMediaCode(programChannel: $programChannel, code: $mediaCode));
                }

                if (empty($ids)) {
                    $ids = [0]; // WORKAROUND : WHERE s.id in () throw SQL error. So I put 0 (unused id) to get request false
                }

                $qb->andWhere($qb->expr()->in("{$alias}.id", $ids));

                return $qb;
            }
        } else {
            $ids = [];
            foreach ($programChannels as $programChannel) {
                $ids = array_merge($ids, $mediaRepository->findIdsStudentWithMediaState(programChannel: $programChannel, state: $media));
            }

            if (!empty($ids)) {
                $qb->andWhere($qb->expr()->in("{$alias}.id", $ids));
            }
        }

        return $qb;
    }

    public function getStudentState(Student $student): string
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.state');
        $qb->where($qb->expr()->eq('s.id', ':_id'))
            ->setParameter('_id', $student->getId());

        return $qb->getQuery()->getOneOrNullResult()['state'];
    }

    public function getStudentProgramChannel(Student $student): ProgramChannel
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.programChannel', 'program_channel')
            ->select('program_channel.id');
        $qb->where($qb->expr()->eq('s.id', ':_id'))
            ->setParameter('_id', $student->getId());

        $id = $qb->getQuery()->getOneOrNullResult()['id'];

        return $this->_em->getRepository(ProgramChannel::class)->findOneBy(['id' => $id]);
    }

    public function getStudentsListFiltered(array $params): array
    {
        $qb = $this->filterQueryBuilder(query: $this->createQueryBuilder('a'), params: $params);

        $qb = $this->addFilterToSearchStudents(
            qb: $qb,
            alias: 'a',
            intern: $params['filters']['intern']?? true,
            media: $params['filters']['media']?? null,
            mediaCode: $params['filters']['mediaCode']?? null
        );

        return $qb->getQuery()->getResult();
    }

    private function getMediaToExport(array $externalColumnsFiltered, array &$result, array $studentIds): void
    {
        $fields = array_filter($externalColumnsFiltered, function($v) {
            if (str_starts_with($v->getValue(), 'media_')) {
                return $v;
            }
        });

        $mediaCodes = [
            MediaCodeConstants::CODE_JOURNEE_DEFENSE_CITOYENNE,
            MediaCodeConstants::CODE_CROUS,
            MediaCodeConstants::CODE_SHN,
            MediaCodeConstants::CODE_TT,
            MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE,
            MediaCodeConstants::CODE_CERTIFICAT_DOUBLE_PARCOURS,
            MediaCodeConstants::CODE_BAC,
        ];

        $list = $this->_em->getRepository(Media::class)->findMediaToExport(mediaCodes: $mediaCodes, studentIds: $studentIds);

        $result = array_map(function($student) use ($fields, $list) {
            $studentMedias = $list[$student['student_id']]?? [];

            foreach ($fields as $field) {
                $alias = str_replace('.', '_', $field->getValue());
                $student[$alias] = null;
                /**
                 * @var string $mediaCode
                 * @var array $medias
                 */
                foreach ($studentMedias as $mediaCode => $medias) {
                    if (str_contains($alias, $mediaCode)) {
                        if (!$field->isManual()) {
                            foreach ($medias as $media) {
                                // For media type crous get the first state
                                if ($mediaCode === MediaCodeConstants::CODE_CROUS and null !== $student[$alias]) {
                                    continue;
                                }
                                // get last state of media
                                $student[$alias] = $media['media_state'] ?? null;
                            }
                        }

                        // if manual get the second media state
                        $count = count($medias);
                        if ($field->isManual() and $count > 1) {
                            $student[$alias] = $medias[$count - 1]['media_state'] ?? null;
                        }
                    }
                }
            }

            return $student;
        }, $result);
    }

    private function getAdministrativeRecordMediasToExport(string $collection, array &$result, array $studentIds): void
    {
        $qb = $this->_em->getRepository(AdministrativeRecord::class)->createQueryBuilder('administrative_record');
        $qb
            ->select("{$collection}.state as state")
            ->addSelect('student.id as student_id')
            ->leftJoin('administrative_record.student', 'student')
            ->leftJoin("administrative_record.{$collection}", $collection)
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->orderBy('student.id', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $list = [];
        foreach ($r as $media) {
            if (!isset($list[$media['student_id']])) {
                $list[$media['student_id']] = [];
            }

            $list[$media['student_id']][] = $media;
        }

        $result = array_map(function($student) use ($collection, $list) {
            $r = $list[$student['student_id']]?? [];

            $hasAllAccepted = true;
            foreach ($r as $media) {
                if (MediaWorflowStateConstants::STATE_ACCEPTED !== $media['state']) {
                    $hasAllAccepted = false;
                    break;
                }
            }

            $student[$collection] = $hasAllAccepted? 'Oui' : '';

            return $student;
        }, $result);
    }

    private function hasDualCourseOfStudy(array &$result, array $studentIds): void
    {
        $qb = $this->_em->getRepository(AdministrativeRecord::class)->createQueryBuilder('administrative_record');
        $qb
            ->select("{$qb->expr()->count('studentDiplomas')} as count")
            ->addSelect('student.id as student_id')
            ->leftJoin('administrative_record.student', 'student')
            ->leftJoin('administrative_record.studentDiplomas', 'studentDiplomas')
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->groupBy('student_id')
            ->orderBy('student_id', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $list = [];
        foreach ($r as $diploma) {
            $list[$diploma['student_id']] = $diploma;
        }

        $result = array_map(function($student) use ($list) {
            $r = $list[$student['student_id']]?? null;

            $student['studentDiplomas'] = (($r['count']?? 0) > 1)? 'Oui' : '';

            return $student;
        }, $result);
    }

    private function getPrincipalDiplomaToExport(array $fields, array &$result, array $studentIds): void
    {
        $qb = $this->_em->getRepository(StudentDiploma::class)->createQueryBuilder('studentDiploma');
        $qb
            ->select('student.id as student_id')
        ;

        foreach ($fields as $field) {
            $alias = str_replace('.', '_', $field);
            $field = str_replace('studentDiploma_', '', $field);
            $qb
                ->addSelect("{$field} AS {$alias}");
        }
        $qb
            ->join('studentDiploma.administrativeRecord', 'administrativeRecord')
            ->join('administrativeRecord.student', 'student')
            ->join('studentDiploma.diploma', 'diploma')
            ->leftJoin('studentDiploma.diplomaChannel', 'diplomaChannel')
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->orderBy('student_id', 'ASC')
            ->addOrderBy('studentDiploma.dualPathStudentDiploma', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $list = [];
        foreach ($r as $diploma) {
            if (isset($list[$diploma['student_id']])) {
                continue;
            }
            $list[$diploma['student_id']] = $diploma;
        }

        $result = self::updateResult($fields, $list, $result);
    }

    private function getDualDiplomaToExport(array $fields, array &$result, array $studentIds): void
    {
        $qb = $this->_em->getRepository(StudentDiploma::class)->createQueryBuilder('studentDiploma');
        $qb->select('student.id as student_id');

        foreach ($fields as $field) {
            $alias = str_replace('.', '_', $field);
            $field = str_replace('studentDiplomaDual_', '', $field);
            $qb
                ->addSelect("{$field} AS {$alias}");
        }
        $qb
            ->join('studentDiploma.dualPathStudentDiploma', 'dual')
            ->join('dual.administrativeRecord', 'administrativeRecord')
            ->join('administrativeRecord.student', 'student')
            ->join('dual.diploma', 'diploma')
            ->leftJoin('dual.diplomaChannel', 'diplomaChannel')
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->andWhere($qb->expr()->isNotNull('studentDiploma.dualPathStudentDiploma'))
            ->orderBy('student_id', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $list = [];
        foreach ($r as $diploma) {
            $list[$diploma['student_id']] = $diploma;
        }

        $result = self::updateResult($fields, $list, $result);
    }

    private function getBacSupsToExport(array $fields, array &$result, array $studentIds): void
    {
        $mediaCodes = [
            MediaCodeConstants::CODE_BULLETIN_L1,
            MediaCodeConstants::CODE_BULLETIN_L1_S1,
            MediaCodeConstants::CODE_BULLETIN_L1_S2,
            MediaCodeConstants::CODE_BULLETIN_L2,
            MediaCodeConstants::CODE_BULLETIN_L2_S3,
            MediaCodeConstants::CODE_BULLETIN_L2_S4,
            MediaCodeConstants::CODE_BULLETIN_L3,
            MediaCodeConstants::CODE_BULLETIN_L3_S5,
            MediaCodeConstants::CODE_BULLETIN_L3_S6,
            MediaCodeConstants::CODE_BULLETIN_M1,
            MediaCodeConstants::CODE_BULLETIN_M1_S1,
            MediaCodeConstants::CODE_BULLETIN_M1_S2,
            MediaCodeConstants::CODE_BULLETIN_M2,
            MediaCodeConstants::CODE_BULLETIN_M2_S3,
            MediaCodeConstants::CODE_BULLETIN_M2_S4,
        ];
        $mediaList = $this->_em->getRepository(Media::class)->findMediaToExport(mediaCodes: $mediaCodes, studentIds: $studentIds);

        $qb = $this->_em->getRepository(BacSup::class)->createQueryBuilder('bac_sup');
        $qb
            ->join('bac_sup.diploma', 'diploma')
            ->join('bac_sup.diplomaChannel', 'diploma_channel')
            ->join('bac_sup.cv', 'cv')
            ->join('cv.student', 'student')
            ->leftJoin('bac_sup.schoolReports', 'school_reports')
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->orderBy('student.id', 'ASC')
            ->addOrderBy('bac_sup.year', 'ASC')
            ->addOrderBy('bac_sup.dualPathBacSup', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $bacSupList = [];
        foreach ($r as $bacSup) {
            if (!isset($bacSupList[$bacSup->getCV()->getStudent()->getId()])) {
                $bacSupList[$bacSup->getCV()->getStudent()->getId()] = [];
            }

            $bacSupList[$bacSup->getCV()->getStudent()->getId()][] = $bacSup;
        }
        $list = [];

        foreach ($bacSupList as $studentId => $bacSups) {
            $index = 0;
            /** @var BacSup $bacSup */
            foreach($bacSups as $bacSup) {
                if (null !== $bacSup->getParent()) {
                    continue;
                }
                $label = match ($index) {
                    0 => 'L1',
                    1 => 'L2',
                    2 => 'L3',
                    3 => 'M1',
                    4 => 'M2',
                    default => ''
                };

                /** @var null|SchoolReport $firstBacSupSchoolReport */
                $firstBacSupSchoolReport = $bacSup->getSchoolReports()->get(0);
                /** @var null|SchoolReport $secondBacSupSchoolReport */
                $secondBacSupSchoolReport = $bacSup->getSchoolReports()->get(1);
                /** @var null|BacSup $dual */
                $dual = $bacSup->getDualPathBacSup();
                /** @var null|SchoolReport $firstDualSchoolReport */
                $firstDualSchoolReport = $dual?->getSchoolReports()->get(0);
                /** @var null|SchoolReport $secondDualSchoolReport */
                $secondDualSchoolReport = $dual?->getSchoolReports()->get(1);

                $list[$studentId] = [
                    "bacSups_{$index}_year" => $bacSup->getYear(),
                    "bacSups_{$index}_diploma" => $bacSup->getDiploma()->getName(),
                    "bacSups_{$index}_diplomaChannel" => $bacSup->getDiplomaChannel()->getName(),
                    "bacSups_{$index}_type" => $bacSup->getType(),
                    "bacSups_{$index}_schoolReports_{$label}_{$label}S1_score" => $firstBacSupSchoolReport?->getScore()?? null,
                    "bacSups_{$index}_schoolReports_{$label}_{$label}S1_scoreRetained" => $firstBacSupSchoolReport?->getScoreRetained()?? null,
                    "bacSups_{$index}_media_{$label}_{$label}S1" => null,
                    "bacSups_{$index}_schoolReports_{$label}S2_score" => $secondBacSupSchoolReport?->getScore()?? null,
                    "bacSups_{$index}_schoolReports_{$label}S2_scoreRetained" => $secondBacSupSchoolReport?->getScoreRetained()?? null,
                    "bacSups_{$index}_media_{$label}S2" => null,
                    "bacSups_{$index}_dualPathBacSup" => (null !== $dual) ? 'Oui' : 'Non',
                    "bacSups_{$index}_dualPathBacSup_diploma" => $dual?->getDiploma()->getName(),
                    "bacSups_{$index}_dualPathBacSup_diplomaChannel" => $dual?->getDiplomaChannel()->getName(),
                    "bacSups_{$index}_dualPathBacSup_type" => $dual?->getType(),
                    "bacSups_{$index}_dualPathBacSup_{$label}_{$label}S1_score" => $firstDualSchoolReport?->getScore()?? null,
                    "bacSups_{$index}_dualPathBacSup_{$label}_{$label}S1_scoreRetained" => $firstDualSchoolReport?->getScoreRetained()?? null,
                    "bacSups_{$index}_dualPathBacSup_media_{$label}_{$label}S1" => null,
                    "bacSups_{$index}_dualPathBacSup_{$label}S2_score" => $secondDualSchoolReport?->getScore()?? null,
                    "bacSups_{$index}_dualPathBacSup_{$label}S2_scoreRetained" => $secondDualSchoolReport?->getScoreRetained()?? null,
                    "bacSups_{$index}_dualPathBacSup_media_{$label}S2" => null,
                ];

                if (isset($mediaList[$studentId])) {
                    foreach ($mediaList[$studentId] as $medias) {
                        foreach($medias as $media) {
                            $bacSupKey = match ($media['media_code']) {
                                MediaCodeConstants::CODE_BULLETIN_L1,
                                MediaCodeConstants::CODE_BULLETIN_L1_S1,
                                MediaCodeConstants::CODE_BULLETIN_L2,
                                MediaCodeConstants::CODE_BULLETIN_L2_S3,
                                MediaCodeConstants::CODE_BULLETIN_L3,
                                MediaCodeConstants::CODE_BULLETIN_L3_S5,
                                MediaCodeConstants::CODE_BULLETIN_M1,
                                MediaCodeConstants::CODE_BULLETIN_M1_S1,
                                MediaCodeConstants::CODE_BULLETIN_M2,
                                MediaCodeConstants::CODE_BULLETIN_M2_S3,
                                => "bacSups_{$index}_media_{$label}_{$label}S1",
                                default => "bacSups_{$index}_media_{$label}S2",
                            };

                            if (isset($list[$studentId][$bacSupKey]) and $list[$studentId][$bacSupKey] === MediaWorflowStateConstants::STATE_ACCEPTED) {
                                continue;
                            }

                            $list[$studentId][$bacSupKey] = $media['media_state'];
                        }
                    }
                }

                $index++;
            }

        }

        $result = self::updateResult($fields, $list, $result);
    }

    private function getExperiences(array &$result, array $studentIds): void
    {
        $qb = $this->_em->getRepository(Cv::class)->createQueryBuilder('cv');
        $qb
            ->select("{$qb->expr()->count('experiences')} as count")
            ->addSelect('student.id as student_id')
            ->join('cv.student', 'student')
            ->leftJoin('cv.experiences', 'experiences')
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->groupBy('student_id')
            ->orderBy('student_id', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $list = [];
        foreach ($r as $cv) {
            $list[$cv['student_id']] = $cv;
        }
        $result = array_map(function ($student) use ($list) {
            $r = $list[$student['student_id']] ?? null;

            $student['experiences'] = (($r['count'] ?? 0) > 1) ? 'Oui' : 'Non';

            return $student;
        }, $result);
    }

    private function getLanguages(array &$result, array $studentIds): void
    {
        $qb = $this->_em->getRepository(Cv::class)->createQueryBuilder('cv');
        $qb
            ->select("language.label as label")
            ->addSelect('student.id as student_id')
            ->join('cv.student', 'student')
            ->join('cv.languages', 'language')
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->groupBy('student_id')
            ->addGroupBy('label')
            ->orderBy('student_id', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $list = [];
        foreach ($r as $cv) {
            if (!isset($list[$cv['student_id']])) {
                $list[$cv['student_id']] = [];
            }
            $list[$cv['student_id']][] = $cv;
        }
        $result = array_map(function ($student) use ($list) {
            $r = $list[$student['student_id']] ?? [];
            $student['languages'] = null;

            foreach ($r as $cv) {
                $student['languages'] .= " / {$cv['label']}";
            }

            if (null !== $student['languages']) {
                $student['languages'] = trim($student['languages'], ' /');
            }

            return $student;
        }, $result);
    }

    private function getExamStudentToExport(array $fields, array &$result, array $studentIds, ?string $type = ExamSessionTypeCodeConstants::ANG): void
    {
        $qb = $this->_em->getRepository(ExamStudent::class)->createQueryBuilder('examStudent');
        $qb
            ->select('student.id as student_id')
            ->addSelect('examSession.id as examSession_id')
            ->addSelect('examSession.type as examSession_type')
            ->addSelect('examSession.price as examSession_price')
            ->addSelect('examSession.priceLink as examSession_price_link')
            ->addSelect('examSession.city as examSession_city')
            ->addSelect('examSessionType.code as examSessionType_code')
        ;

        $selectorSelected = [];
        foreach ($fields as $field) {
            if (str_contains($field, 'order.state')) {
                continue;
            }
            $selector = preg_replace('/(\w+)_(\d+)_/', '', $field);
            if (!is_string($selector)) {
                continue;
            }
            if (in_array($selector, $selectorSelected, true)) {
                continue;
            }

            $alias = str_replace('.', '_', $selector);
            $qb->addSelect("{$selector} AS {$alias}");

            $selectorSelected[] = $selector;
        }

        $qb
            ->join('examStudent.student', 'student')
            ->join('examStudent.examSession', 'examSession')
            ->join('examSession.examClassification', 'examClassification')
            ->join('examClassification.examSessionType', 'examSessionType')
            ->leftJoin('examSession.campus', 'campus')
            ->leftJoin('examStudent.media', 'media')
            ->where($qb->expr()->in('student.id', $studentIds))
            ->andWhere($qb->expr()->eq('examSessionType.code', ':type'))
            ->setParameter('type', $type)
            ->orderBy('student_id', 'ASC')
            ->addOrderBy('examClassification.name', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        try {
            $r = $query->getResult();
        } catch (\Exception $e) {
            return;
        }

        $examList = [];
        foreach ($r as $exam) {
            if (!isset($examList[$exam['student_id']])) {
                $examList[$exam['student_id']] = [];
            }

            $orderStateLabel = null;
            if ($exam['examSession_type'] === ExamSessionTypeConstants::TYPE_INSIDE) {
                $order = $this->_em->getRepository(Order::class)->findOneBy([
                    'student' => $exam['student_id'],
                    'examSession' => $exam['examSession_id'],
                    'type' => OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION,
                ]);
                $state = $order?->getState();
                if (null !== $exam['examSession_price']) {
                    $orderStateLabel = match($state) {
                        OrderWorkflowStateConstants::STATE_VALIDATED => 'Payée',
                        default => 'A payer'
                    };
                }
                if (null === $exam['examSession_price'] and null !== $exam['examSession_price_link']) {
                    $orderStateLabel = match($state) {
                        OrderWorkflowStateConstants::STATE_VALIDATED => 'Confirmée',
                        default => 'A confirmer'
                    };
                }
            }

            $exam['order_state'] = $orderStateLabel;

            $examList[$exam['student_id']][] = $exam;
        }

        $list = [];
        foreach($examList as $studentId => $exams) {
            foreach ($exams as $key => $exam) {
                if (
                    isset($exam['examSession_type']) and
                    isset($exam['campus_name']) and
                    isset($exam['examSession_city']) and
                    $exam['examSession_type'] === ExamSessionTypeConstants::TYPE_OUTSIDE) {
                    $exam['campus_name'] = $exam['examSession_city'];
                }

                $index = $key + 1;
                $examFields = preg_grep("/^(.+)?_{$index}_.*/", $fields);
                if (false !== $examFields and count($examFields) > 0) {
                    /** @var string $examField */
                    foreach ($examFields as $examField) {
                        $selector = preg_replace('/(\w+)_(\d+)_/', '', $examField);
                        if (!is_string($selector)) {
                            continue;
                        }
                        $alias = str_replace('.', '_', $selector);
                        $list[$studentId][str_replace('.', '_', $examField)] = $exam[$alias] ?? null;
                    }
                }
            }
        }

        $result = self::updateResult($fields, $list, $result);
    }

    public function getAdmissibleByProgramChannel(ProgramChannel $programChannel, bool $registeredForExams = false): array
    {
        $qb = $this->createQueryBuilder('student');
        $qb
            ->andWhere($qb->expr()->in('student.state', ':_states'))
            ->andWhere($qb->expr()->in('student.programChannel', ':_program_channel'));
        if (true === $registeredForExams) {
            $qb->setParameters([
                '_states' => StudentWorkflowStateConstants::REGISTERED_EO,
                '_program_channel' => $programChannel->getId()
            ]);
        } else {
            $qb->setParameters([
                '_states' => StudentWorkflowStateConstants::STATE_ADMISSIBLE,
                '_program_channel' => $programChannel->getId()
            ]);
        }

        $qb->groupBy('student.id');

        return $qb->getQuery()->getResult();
    }
}
