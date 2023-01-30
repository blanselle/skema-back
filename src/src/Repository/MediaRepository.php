<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants\Dashboard\DashboardMediaLabelConstants;
use App\Constants\Exam\ExamSessionTypeCodeConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\CV\Bac\Bac;
use App\Entity\Exam\ExamStudent;
use App\Entity\Media;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function findOrphanJdcMedia(Student $student): array
    {
        $qb = $this->createQueryBuilder('m');
        $subQb = $this->_em->getRepository(AdministrativeRecord::class)->createQueryBuilder('ar')
            ->select('m.id')
            ->andWhere($qb->expr()->eq('m', 'ar.jdc'))
            ->getDQL()
        ;

        $qb
            ->andWhere($qb->expr()->notIn('m.id', $subQb))
            ->andWhere($qb->expr()->eq('m.student', ':student'))
            ->andWhere($qb->expr()->like('m.code', ":_code"))
            ->setParameter('student', $student)
            ->setParameter('_code', MediaCodeConstants::CODE_JOURNEE_DEFENSE_CITOYENNE)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findOrphanBacMedia(Student $student): array
    {
        $qb = $this->createQueryBuilder('m');
        $subQb = $this->_em->getRepository(Bac::class)->createQueryBuilder('bac')
            ->select('m.id')
            ->andWhere($qb->expr()->eq('m', 'bac.media'))
            ->getDQL()
        ;

        $qb
            ->andWhere($qb->expr()->notIn('m.id', $subQb))
            ->andWhere($qb->expr()->eq('m.student', ':student'))
            ->andWhere($qb->expr()->like('m.code', ":_code"))
            ->setParameter('student', $student)
            ->setParameter('_code', MediaCodeConstants::CODE_BAC)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findOrphanExamStudentMedia(ExamStudent $examStudent): array
    {
        $qb = $this->createQueryBuilder('m'); 

        $subQb = $this->_em->getRepository(ExamStudent::class)->createQueryBuilder('es')
            ->select('m.id')
            ->andWhere($qb->expr()->eq('m', 'es.media'))
            ->getDQL()
        ;

        $qb
            ->andWhere($qb->expr()->notIn('m.id', $subQb))
            ->andWhere($qb->expr()->eq('m.student', ':student'))
            ->andWhere($qb->expr()->in('m.code', [
                MediaCodeConstants::CODE_ATTESTATION_ANGLAIS,
                MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT,
            ]))
            ->setParameter('student', $examStudent->getStudent())
        ;
        
        return $qb->getQuery()->getResult();
    }

    public function findNbStudentWithMediaMissing(ProgramChannel $programChannel): array
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            SELECT 'CERTIFICAT_ELIGIBILITE' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaCertificatMissing()}
            UNION
            SELECT 'SHN' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaShnMissing()}
            UNION
            SELECT 'TT' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaTtMissing()}
            UNION
            SELECT 'CROUS' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaCrousMissing()}
            UNION
            SELECT 'BAC' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaBacMissing()}
            UNION
            SELECT 'BULLETIN' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaBulletinMissing()}
            UNION
            SELECT 'ATTESTATION_ANGLAIS' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaAttestationAnglaisMissing(externalSession: true)}
            UNION
            SELECT 'ATTESTATION_MANAGEMENT' as code, count(DISTINCT s.id) as count
            {$this->sqlStudentMediaAttestationManagementMissing(externalSession: true)}
		");

        $result = $query->executeQuery(['programchannel' => $programChannel->getId()]);

        return $this->orderDataWithOrderGetConst($result->fetchAllAssociative());
    }

    public function findIdsStudentWithMediaMissing(ProgramChannel $programChannel, string $mediaCode, ?bool $externalSession = false): array
    {
        switch ($mediaCode) {
            case MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE:
                $sql = $this->sqlStudentMediaCertificatMissing();
                break;
            case MediaCodeConstants::CODE_SHN:
                $sql = $this->sqlStudentMediaShnMissing();
                break;
            case MediaCodeConstants::CODE_TT:
                $sql = $this->sqlStudentMediaTtMissing();
                break;
            case MediaCodeConstants::CODE_CROUS:
                $sql = $this->sqlStudentMediaCrousMissing();
                break;
            case MediaCodeConstants::CODE_BAC:
                $sql = $this->sqlStudentMediaBacMissing();
                break;
            case 'bulletin':
                $sql = $this->sqlStudentMediaBulletinMissing();
                break;
            case MediaCodeConstants::CODE_ATTESTATION_ANGLAIS:
                $sql = $this->sqlStudentMediaAttestationAnglaisMissing(externalSession: $externalSession);
                break;
            case MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT:
                $sql = $this->sqlStudentMediaAttestationManagementMissing(externalSession: $externalSession);
                break;
            default:
                return [];
        }
        $connection = $this->_em->getConnection();

        $query = $connection->prepare("
            SELECT DISTINCT s.id
            $sql
		");

        $result = $query->executeQuery(['programchannel' => $programChannel->getId()]);

        $result = $result->fetchAllAssociative();

        return array_map(function ($item) {
            return $item['id'];
        }, $result);
    }

    public function findIdsStudentWithMediaToValidate(ProgramChannel $programChannel, string $codeMedia, ?bool $externalSession = false): array
    {
        $connection = $this->_em->getConnection();

        $sql = "
            SELECT DISTINCT s.id
            {$this->sqlMedia(code: $codeMedia, state: 'to_check', externalSession: $externalSession)}
        ";

        $query = $connection->prepare($sql);

        $result = $query->executeQuery(['programchannel' => $programChannel->getId()]);

        $result = $result->fetchAllAssociative();

        return array_map(function ($item) {
            return $item['id'];
        }, $result);
    }

    public function findNbStudentWithMediaToValidate(ProgramChannel $programChannel): array
    {
        $connection = $this->_em->getConnection();

        $codes = [
            'certificat_eligibilite',
            'shn',
            'tt',
            'crous',
            'bac',
            'bulletin%',
            'attestation_anglais',
            'attestation_management',
        ];

        $sql = "
            SELECT '".strtoupper(str_replace('%', '', $codes[0]))."' as code, count(m.id) as count
            {$this->sqlMedia(code: $codes[0], state: 'to_check')}
        ";
        for ($i = 1; $i < count($codes); $i++) {
            $externalSession = false;
            if ($codes[$i] === MediaCodeConstants::CODE_ATTESTATION_ANGLAIS or $codes[$i] === MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT) {
                $externalSession = true;
            }

            $sql .= "\n UNION \n";
            $sql .= "SELECT '".strtoupper(str_replace('%', '', $codes[$i]))."' as code, count(m.id) as count";
            $sql .= $this->sqlMedia(code: $codes[$i], state: 'to_check', externalSession: $externalSession);
        }

        $query = $connection->prepare($sql);

        $result = $query->executeQuery(['programchannel' => $programChannel->getId()]);

        return $this->orderDataWithOrderGetConst($result->fetchAllAssociative());
    }

    public function findIdsStudentWithMediaCode(ProgramChannel $programChannel, string $code): array
    {
        $connection = $this->_em->getConnection();

        $sql = "
            SELECT DISTINCT s.id
            {$this->sqlMedia(code: "{$code}%")}
        ";

        $query = $connection->prepare($sql);

        $result = $query->executeQuery(['programchannel' => $programChannel->getId()]);

        $result = $result->fetchAllAssociative();

        return array_map(function ($item) {
            return $item['id'];
        }, $result);
    }

    public function findIdsStudentWithMediaState(ProgramChannel $programChannel, string $state): array
    {
        $connection = $this->_em->getConnection();
        if ($state === 'toValidate') {
            $sql = " SELECT DISTINCT s.id {$this->sqlMedia(state: 'to_check')} ";
            $query = $connection->prepare($sql);

            $result = $query->executeQuery(['programchannel' => $programChannel->getId()]);

            $result = $result->fetchAllAssociative();

            return array_map(function ($item) {
                return $item['id'];
            }, $result);
        }

        $codes = [
            'certificat_eligibilite',
            'shn',
            'tt',
            'crous',
            'bac',
            'bulletin%',
            'attestation_anglais',
            'attestation_management',
        ];

        $ids = [];
        foreach($codes as $code) {
            $sql = "SELECT DISTINCT s.id 
            {$this->sqlHeader()}
            WHERE s.program_channel_id = :programchannel
            {$this->sqlStudentWithoutMediaOrRejected(code: $code)}
            ";
            $query = $connection->prepare($sql);

            $result = $query->executeQuery(['programchannel' => $programChannel->getId()]);

            $result = $result->fetchAllAssociative();
            $ids = array_merge($ids, array_map(function ($item) {
                return $item['id'];
            }, $result));
        }

        return [];
    }

    public function fetchDocumentsToCompleteCandidacy(Student $student): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->where($qb->expr()->eq('m.student', ':_student'))
            ->andWhere($qb->expr()->neq('m.state', ':_cancelled'))
            ->andWhere($qb->expr()->notIn('m.code', ':_codes'))
            ->setParameters([
                '_student' => $student,
                '_cancelled' => MediaWorflowStateConstants::STATE_CANCELLED,
                '_codes' => [
                    MediaCodeConstants::CODE_ID_CARD,
                    MediaCodeConstants::CODE_JOURNEE_DEFENSE_CITOYENNE,
                ]
            ]);

        return $qb->getQuery()->getResult();
    }

    public function findMediaToExport(array $mediaCodes, array $studentIds): array
    {
        $qb = $this->createQueryBuilder('media');
        $qb
            ->select('media.code as media_code, media.state as media_state, student.id as student_id')
            ->innerJoin('media.student', 'student')
            ->where($qb->expr()->in('media.code', $mediaCodes))
            ->andWhere($qb->expr()->in('student.id', $studentIds))
            ->andWhere($qb->expr()->neq('media.state', ':_state'))
            ->setParameter('_state', MediaWorflowStateConstants::STATE_CANCELLED)
            ->orderBy('student.id', 'ASC')
            ->addOrderBy('media.code', 'ASC')
        ;

        $query = $qb->getQuery();
        // Disable doctrine cache
        $query->disableResultCache();

        $r = $query->getResult();
        $list = [];
        foreach ($r as $media) {
            if (!isset($list[$media['student_id']])) {
                $list[$media['student_id']] = [];
            }
            $list[$media['student_id']][$media['media_code']][] = $media;
        }

        return $list;
    }

    // --- SQL parts

    private function sqlStudentMediaCertificatMissing(): string
    {
        return "
            {$this->sqlHeader()}
            WHERE s.program_channel_id = :programchannel
            {$this->sqlStudentState('exemption')}
            {$this->sqlStudentState('rejected%')}
            {$this->sqlStudentState('resigned%')}
            {$this->sqlStudentWithoutMediaOrRejected('certificat_eligibilite')}
        ";
    }

    private function sqlStudentMediaShnMissing(): string
    {
        return "
            {$this->sqlHeader()}
            LEFT JOIN administrative_record ar ON ar.student_id = s.id
            WHERE s.program_channel_id = :programchannel
            AND ar.high_level_sportsman = true
            {$this->sqlStudentWithoutMediaOrRejected('shn')}
        ";
    }

    private function sqlStudentMediaTtMissing(): string
    {
        return "
            {$this->sqlHeader()}
            LEFT JOIN administrative_record ar ON ar.student_id = s.id
            WHERE s.program_channel_id = :programchannel
            AND ar.third_time = '1'
            {$this->sqlStudentWithoutMediaOrRejected('tt')}
        ";
    }

    private function sqlStudentMediaCrousMissing(): string
    {
        return "
            {$this->sqlHeader()}
            LEFT JOIN administrative_record ar ON ar.student_id = s.id
            WHERE s.program_channel_id = :programchannel
            AND ar.scholar_ship = '1'
            {$this->sqlStudentWithoutMediaOrRejected('crous')}
        ";
    }

    private function sqlStudentMediaBacMissing(): string
    {
        return "
            {$this->sqlHeader()}
            WHERE s.program_channel_id = :programchannel
            {$this->sqlStudentState('exemption')}
            {$this->sqlStudentState('rejected%')}
            {$this->sqlStudentState('created%')}
            {$this->sqlStudentState('check_diploma')}
            {$this->sqlStudentState('resigned%')}
            {$this->sqlStudentWithoutMediaOrRejected('bac')}
        ";
    }

    private function sqlStudentMediaBulletinMissing(): string
    {
        return "
            {$this->sqlHeader()}
            WHERE s.program_channel_id = :programchannel
            {$this->sqlStudentState('exemption')}
            {$this->sqlStudentState('rejected%')}
            {$this->sqlStudentState('created%')}
            {$this->sqlStudentState('check_diploma')}
            {$this->sqlStudentState('resigned%')}
            {$this->sqlStudentWithoutMediaOrRejected('bulletin%')}
        ";
    }

    private function sqlStudentMediaAttestationAnglaisMissing(?bool $externalSession = false): string
    {
        if ($externalSession) {
            return "
                FROM student s 
                INNER JOIN exam_student es ON es.student_id = s.id
                INNER JOIN exam_session ess ON ess.id = es.exam_session_id
                INNER JOIN exam_classification ec ON ess.exam_classification_id = ec.id
                INNER JOIN exam_session_type est ON ec.exam_session_type_id = est.id
                LEFT JOIN media m ON es.media_id=m.id
                WHERE s.program_channel_id = :programchannel
                AND ess.type LIKE '" . ExamSessionTypeConstants::TYPE_OUTSIDE . "' 
                AND est.code LIKE '" . ExamSessionTypeCodeConstants::ANG . "'
                {$this->sqlStudentState('exemption')}
                {$this->sqlStudentState('rejected%')}
                {$this->sqlStudentState('created%')}
                {$this->sqlStudentState('check_diploma')}
                {$this->sqlStudentState('resigned%')}
                AND (m.code LIKE '" . MediaCodeConstants::CODE_ATTESTATION_ANGLAIS . "' AND m.state LIKE '" . MediaWorflowStateConstants::STATE_REJECTED . "' OR es.media_id IS NULL)
            ";
        }

        return "
            {$this->sqlHeader()}
            WHERE s.program_channel_id = :programchannel
            {$this->sqlStudentState('exemption')}
            {$this->sqlStudentState('rejected%')}
            {$this->sqlStudentState('created%')}
            {$this->sqlStudentState('check_diploma')}
            {$this->sqlStudentState('resigned%')}
            {$this->sqlStudentWithoutMediaOrRejected(code: MediaCodeConstants::CODE_ATTESTATION_ANGLAIS)}
        ";
    }

    private function sqlStudentMediaAttestationManagementMissing(?bool $externalSession = false): string
    {
        if ($externalSession) {
            return "
                FROM student s 
                INNER JOIN exam_student es ON es.student_id = s.id
                INNER JOIN exam_session ess ON ess.id = es.exam_session_id
                INNER JOIN exam_classification ec ON ess.exam_classification_id = ec.id
                INNER JOIN exam_session_type est ON ec.exam_session_type_id = est.id
                LEFT JOIN media m ON es.media_id=m.id
                WHERE s.program_channel_id = :programchannel
                AND ess.type LIKE '" . ExamSessionTypeConstants::TYPE_OUTSIDE . "' 
                AND est.code LIKE '" . ExamSessionTypeCodeConstants::MANAGEMENT . "'
                AND ec.key LIKE 'gmat'
                {$this->sqlStudentState('exemption')}
                {$this->sqlStudentState('rejected%')}
                {$this->sqlStudentState('created%')}
                {$this->sqlStudentState('check_diploma')}
                {$this->sqlStudentState('resigned%')}
                AND (m.code LIKE '" . MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT . "' AND m.state LIKE '" . MediaWorflowStateConstants::STATE_REJECTED . "' OR es.media_id IS NULL)
            ";
        }

        return "
            {$this->sqlHeader()}
            WHERE s.program_channel_id = :programchannel
            {$this->sqlStudentState('exemption')}
            {$this->sqlStudentState('rejected%')}
            {$this->sqlStudentState('created%')}
            {$this->sqlStudentState('check_diploma')}
            {$this->sqlStudentState('resigned%')}
            {$this->sqlStudentWithoutMediaOrRejected(code: MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT)}
        ";
    }

    private function sqlMedia(?string $code = null, ?string $state = null, ?bool $externalSession = false): string
    {
        $where = false;
        $sql = "
            FROM media m
            LEFT JOIN student s ON m.student_id = s.id  
        ";

        if (true === $externalSession and $code === MediaCodeConstants::CODE_ATTESTATION_ANGLAIS) {
            $this->sqlExternalSessionAng(sql: $sql);
            $where = true;
        }

        if (true === $externalSession and $code === MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT) {
            $this->sqlExternalSessionManagement(sql: $sql);
            $where = true;
        }

        if (null !== $code) {
            $sql .= ($where === false)? " WHERE " : " AND ";
            $sql .= "m.code LIKE '{$code}%' ";
            $where |= ($where === false);
        }

        if (null !== $state) {
            $sql .= ((bool)$where === false)? " WHERE " : " AND ";
            $sql .= "m.state LIKE '{$state}' ";
            $where |= ((bool)$where === false);
        } else {
            $sql .= ((bool)$where === false)? " WHERE " : " AND ";
            $sql .= "m.state NOT LIKE '" . MediaWorflowStateConstants::STATE_CANCELLED . "'";
            $where |= ((bool)$where === false);
        }

        $sql .= ((bool)$where === false)? " WHERE " : " AND ";
        $sql .= "s.program_channel_id = :programchannel ";

        return $sql;
    }

    private function sqlExternalSessionAng(string &$sql): void
    {
        $externalType = ExamSessionTypeConstants::TYPE_OUTSIDE;
        $externalCode = ExamSessionTypeCodeConstants::ANG;
        $sql .= "
            INNER JOIN exam_student es ON es.student_id = s.id 
            INNER JOIN exam_session ess ON ess.id = es.exam_session_id 
            INNER JOIN exam_classification ec ON ess.exam_classification_id = ec.id 
            INNER JOIN exam_session_type est ON ec.exam_session_type_id = est.id 
            WHERE ess.type LIKE '{$externalType}' AND est.code LIKE '{$externalCode}' 
            {$this->sqlStudentState('created%')}
            {$this->sqlStudentState('exemption')}
            {$this->sqlStudentState('rejected%')}
            {$this->sqlStudentState('resigned%')}
            {$this->sqlStudentState('check_diploma')}
        ";
    }

    private function sqlExternalSessionManagement(string &$sql): void
    {
        $externalType = ExamSessionTypeConstants::TYPE_OUTSIDE;
        $externalCode = ExamSessionTypeCodeConstants::MANAGEMENT;
        $sql .= "
            INNER JOIN exam_student es ON es.student_id = s.id 
            INNER JOIN exam_session ess ON ess.id = es.exam_session_id 
            INNER JOIN exam_classification ec ON ess.exam_classification_id = ec.id 
            INNER JOIN exam_session_type est ON ec.exam_session_type_id = est.id 
            WHERE ess.type LIKE '{$externalType}' AND est.code LIKE '{$externalCode}' AND ec.key LIKE 'gmat' 
            {$this->sqlStudentState('created%')}
            {$this->sqlStudentState('exemption')}
            {$this->sqlStudentState('rejected%')}
            {$this->sqlStudentState('resigned%')}
            {$this->sqlStudentState('check_diploma')}
        ";
    }

    private function sqlStudentWithoutMediaOrRejected(?string $code = null): string
    {
        $subQuery = "SELECT m.student_id FROM media m ";

        $subQuery .= " WHERE m.student_id IS NOT NULL AND m.state NOT LIKE 'rejected' AND m.state NOT LIKE '" . MediaWorflowStateConstants::STATE_CANCELLED . "'";
        if (null !== $code) {
            $subQuery .= " AND m.code LIKE '{$code}'";
        }

        return " AND s.id NOT IN ({$subQuery}) ";
    }

    private function sqlHeader(): string
    {
        return "
            FROM student s
            LEFT JOIN media m ON m.student_id = s.id
        ";
    }

    private function sqlStudentState(string $state): string
    {
        return "AND s.state NOT LIKE '{$state}'";
    }

    private function orderDataWithOrderGetConst(array $data): array
    {
        $return = [];
        foreach (DashboardMediaLabelConstants::getConsts() as $key => $value)
        {
            foreach ($data as $item) {
                if ($key === $item['code']) {
                    $return[] = $item;
                }
            }

        }

        return $return;
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('m');
        $qb
            ->andWhere($qb->expr()->eq('m.id', ':_id'))
            ->setParameter('_id', $id)
        ;

        return $qb->getQuery()->getOneOrNullResult()?->getStudent();
    }
}
