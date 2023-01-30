<?php

namespace App\Command;

use App\Helper\DbHelper;
use App\Message\CvCalculationMessage;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import',
    description: 'Import data from Skema 2021-2022.'
)]
class DataImportCommand extends Command
{
    private array $cvIds = [];

    public function __construct(
        private EntityManagerInterface $em,
        private DbHelper $dbHelper,
        private Connection $connection,
        private MessageBusInterface $bus,
        string $name = null)
    {
        parent::__construct($name);
        $this->connection = $this->em->getConnection();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->dbHelper->resetDb();
        $this->importCandidat();
        $this->importCandidatDossierAdmin();
        $this->importCandidatLanguages();
        $this->importCandidatBac();
        $this->importBacSup();
        $this->importCandidatXp();
        $this->importCandidatScores();
        $this->dispatchCvCalculation();


        return Command::SUCCESS;
    }

    private function createMedia(int $studentId, string $mediaCode): int
    {
        $query = "insert into media 
                (
                     id, 
                     student_id, 
                     file, 
                     original_name, 
                     type, 
                     state, 
                     code, 
                     created_at, 
                     updated_at
                ) values (
                    nextval('media_id_seq'),
                    ".$studentId.",
                    '/tmp/media.txt',
                    'media.txt',
                    'document_to_validate',
                    'accepted',
                    '".$mediaCode."',
                    now(),
                    now()
                ) 
                returning id   
            ";
        $result = $this->connection->executeQuery($query);

        return $result->fetchAssociative()['id'];
    }

    private function getStudentIdFromEmail(string $email): int
    {
        $query = sprintf("select student_id from users where email = '%s'", $email);
        $result = $this->connection->executeQuery($query);

        return $result->fetchAssociative()['student_id'];
    }

    private function getCvIdFromStudentId(int $studentId): int
    {
        $query = sprintf("select id from cv where student_id = %d", $studentId);
        $result = $this->connection->executeQuery($query);

        return $result->fetchAssociative()['id'];
    }

    private function getCountryId(string $countryId): int
    {
        $query = sprintf("select id from country where id_country = '%s'", trim($countryId));
        $result = $this->connection->executeQuery($query);

        return $result->fetchAssociative()['id'];
    }

    private function importCandidatScores(): void
    {
        $handle = fopen("data/import/init_candidat_scores.csv", "r");

        $i = 0;
        while (($rawString = fgets($handle)) !== false) {

            $row = str_getcsv($rawString, ';');
            if (0 === $i) {
                $i++;
                continue;
            }

            $studentId = $this->getStudentIdFromEmail($row[0]);

            $sessionTypeTranslation = [
                '1' => 'tage2',
                '2' => 'tagemage',
                '3' => 'gmat',
                '4' => 'toiec',
                '5' => 'toeflibt',
                '6' => 'toefltip',
                '7' => 'ietls',
                '8' => 'icims'
            ];

            $query = "select id from exam_classification where key = '".$sessionTypeTranslation[$row[1]]."'";
            $result = $this->connection->executeQuery($query);
            $examClassificationId = $result->fetchAssociative()['id'];

            $query = "
                insert into exam_session
                    (
                        id,
                        exam_classification_id,
                        date_start,
                        number_of_places,
                        created_at,
                        updated_at,
                        type,
                        distributed
                    ) values (
                        nextval('exam_session_id_seq'),
                        ".$examClassificationId.",
                        '2022-01-01',
                        1,
                        now(),
                        now(),
                        'Extérieur',
                        '0'
                    )
                    returning id
            ";
            $result = $this->connection->executeQuery($query);
            $examSessionId = $result->fetchAssociative()['id'];

            $mediaCode = in_array($row[1], ['4','5','6','7','8'], false) ? 'attestation_anglais' : 'attestation_management';
            $mediaId = $this->createMedia($studentId, $mediaCode);

            $query = "
                insert into exam_student
                    (
                        id,
                        exam_session_id,
                        student_id,
                        created_at,
                        updated_at,
                        media_id,
                        score,
                        specific,
                        confirmed
                    ) values (
                        nextval('exam_student_id_seq'),
                        ".$examSessionId.",
                        ".$studentId.",
                        now(),
                        now(),
                        ".$mediaId.",
                        ".(int)$row[2].",
                        '0',
                        '0'
                    )
            ";

            $this->connection->executeQuery($query);

            $i++;
        }
    }

    private function importCandidatXp(): void
    {
        $handle = fopen("data/import/init_candidat_xp.csv", "r");

        $i = 0;
        while (($rawString = fgets($handle)) !== false) {
            $row = str_getcsv($rawString, ';');
            if (0 === $i) {
                $i++;
                continue;
            }
            $studentId = $this->getStudentIdFromEmail($row[0]);
            $cvId = $this->getCvIdFromStudentId($studentId);

            $query = "
                insert into experience
                    (
                     id,
                     cv_id,
                     establishment,
                     time_type,
                     begin_at,
                     end_at,
                     description,
                     experience_type,
                     hours_per_week,
                     state,
                     duration
                    ) values (
                        nextval('experience_id_seq'),
                        ".$cvId.",
                        '".str_replace("'","''",str_replace("\n"," ", $row[2]))."',
                        '".$row[1]."',
                        '".$row[3]."',
                        '".$row[4]."',
                        '',
                        '".$row[5]."',
                        '".$row[6]."',
                        '".$row[8]."',
                        '".round($row[7])."'
                    )
            
            ";

            $this->connection->executeQuery($query);

            $i++;
        }
    }

    private function importBacSup(): void
    {
        $handle = fopen("data/import/init_candidat_bacSup.csv", "r");
        $i = 0;
        while (($rawString = fgets($handle)) !== false) {
            $row = str_getcsv($rawString, ';');
            if (0 === $i) {
                $i++;
                continue;
            }

            $studentId = $this->getStudentIdFromEmail($row[0]);
            $cvId = $this->getCvIdFromStudentId($studentId);

            $row[2] = str_replace(' (nous contacter)', '', $row[2]);
            $row[3] = str_replace('Autre - Préciser', 'Autre', $row[3]);
            $row[3] = str_replace('COMMERCE/MARKETING', 'Commerce / marketing', $row[3]);
            $row[3] = str_replace('E COMMERCE MARKETING DIGITAL', 'E-Commerce Marketing Digital', $row[3]);
            $row[3] = str_replace('ÉCONOMIE/FINANCE', 'Finance', $row[3]);
            $row[3] = str_replace('Préciser l\'intitulé', 'Précisez l’intitulé', $row[3]);
            $row[3] = str_replace(' Autre', 'Autre', $row[3]);
            $row[4] = substr($row[4], 0, 50);
            $row[4] = substr($row[4], 0, strrpos($row[4], ' '));
            $query = "
                select dc.id as id from diploma_channel dc
                left join diploma_diploma_channel ddc  on ddc.diploma_channel_id = dc.id
                left join diploma d on d.id = ddc.diploma_id
                where upper(unaccent(dc.name)) = upper(unaccent('".str_replace("'","''",trim($row[3]))."')) and upper(unaccent(d.name)) = upper(unaccent('".str_replace("'","''",trim($row[2]))."'))
            ";

            $result = $this->connection->executeQuery($query);
            $data = $result->fetchAssociative();
            $diplomaChannelId = $data ? $data['id'] : '1';

            $query = "select id from diploma where upper(unaccent(name)) = upper(unaccent('".str_replace("'","''",trim($row[2]))."'))";
            $result = $this->connection->executeQuery($query);
            $diplomaId = $result->fetchAssociative()['id'];

            $countryId = $this->getCountryId($row[8]);

            $query = "
                insert into bac_sup
                    (
                         id,
                         diploma_id,
                         diploma_channel_id,
                         cv_id,
                         country_id,
                         establishment,
                         year,
                         postal_code,
                         city,
                         type,
                         detail   
                    ) values 
                    (
                         nextval('bac_sup_id_seq'),
                        ".$diplomaId.",
                        ".$diplomaChannelId.",
                        ".$cvId.",
                        ".$countryId.",
                        '".str_replace("'","''",$row[5])."',
                        ".(int) $row[1].",
                        '".$row[6]."',
                        '".str_replace("'","''",$row[7])."',
                        '".strtolower($row[9])."',
                        '".str_replace("'","''",$row[4])."'    
                    )
                    returning id
            ";
            $result = $this->connection->executeQuery($query);
            $bacSupId = $result->fetchAssociative()['id'];

            $mediaId = $this->createMedia($studentId, 'bulletin_L1_S1');

            $query = "
                insert into school_report
                (
                    id,
                    media_id,
                    bac_sup_id,
                    score,
                    score_retained,
                    additionnal
                ) values (
                    nextval('school_report_id_seq'),
                    ".$mediaId.",
                    ".$bacSupId.",
                    ".$row[10]." ,
                    ".$row[11].",
                    '0'
                )
            ";
            $this->connection->executeQuery($query);

            if ('Semestriel' == $row[9]) {
                $secondMediaId = $this->createMedia($studentId, 'bulletin_L1_S1');
                $query = "
                insert into school_report
                (
                    id,
                    media_id,
                    bac_sup_id,
                    score,
                    score_retained,
                    additionnal
                ) values (
                    nextval('school_report_id_seq'),
                    ".$secondMediaId.",
                    ".$bacSupId.",
                    ".$row[14]." ,
                    ".$row[15].",
                    '0'  
                )
            ";
                $this->connection->executeQuery($query);
            }

            $i++;
        }
    }

    private function importCandidatBac(): void
    {
        $handle = fopen("data/import/init_candidat_bac.csv", "r");

        $i = 0;
        while (($rawString = fgets($handle)) !== false) {

            $row = str_getcsv($rawString, ';');
            if (0 === $i) {
                $i++;
                continue;
            }

            $studentId = $this->getStudentIdFromEmail($row[0]);
            $cvId = $this->getCvIdFromStudentId($studentId);

            $query = "select id from bac_channel where name = '".$row[3]."'";
            $result = $this->connection->executeQuery($query);
            $bacChannelId = $result->fetchAssociative()['id'];

            $mediaId = $this->createMedia($studentId, 'bac');

            $query =  "insert into bac 
                (
                    id,
                    bac_channel_id,
                    cv_id,
                    rewarded_year,
                    ine,
                    bac_distinction_id, 
                    created_at,
                    updated_at,
                    media_id
                ) values (
                    nextval('bac_id_seq'),
                    ".$bacChannelId.",
                    ".$cvId.",
                    '".(int)$row[1]."',
                    '".str_replace("'","''", $row[2])."',
                    ".(int)$row[5].",
                    now(),
                    now(),
                    ".$mediaId."
                )
                returning id
            ";
            $result = $this->connection->executeQuery($query);
            $bacId = $result->fetchAssociative()['id'];

            $row[4] = str_replace('STG', 'STMG', $row[4]);
            $row[4] = str_replace('Autre', 'STMG', $row[4]);
            $row[4] = str_replace('TMD', 'S2TMD', $row[4]);
            $query = sprintf("select id from bac_type where name = '%s'", $row[4]);
            $result = $this->connection->executeQuery($query)->fetchAssociative();

            if (!empty($result)) {
                $bacTypeId = $result['id'];

                $query = sprintf("insert into bac_bac_type (bac_id, bac_type_id) values (%d, %d)", $bacId, $bacTypeId);
                $this->connection->executeQuery($query);
            }

            $i++;
        }
    }

    private function importCandidatLanguages(): void
    {
        $handle = fopen("data/import/init_candidat_languages.csv", "r");

        $i = 0;
        while (($rawString = fgets($handle)) !== false) {
            $row = str_getcsv($rawString, ';');
            if (0 === $i) {
                $i++;
                continue;
            }

            $studentId = $this->getStudentIdFromEmail($row[0]);
            $cvId = $this->getCvIdFromStudentId($studentId);

            $query = "select id from language where code = '".$row[1]."'";
            $result = $this->connection->executeQuery($query);
            $languageId = $result->fetchAssociative()['id'];

            // insert into cv_language
            $query = "insert into cv_language (cv_id, language_id) values (".$cvId.", ".$languageId.")";
            $this->connection->executeQuery($query);

            $i++;
        }
    }

    private function importCandidatDossierAdmin(): void
    {
        $handle = fopen("data/import/init_candidat_dossier_admin.csv", "r");

        $i = 0;
        while (($rawString = fgets($handle)) !== false) {

            $row = str_getcsv($rawString, ';');
            if (0 === $i) {
                $i++;
                continue;
            }

            $studentId = $this->getStudentIdFromEmail($row[0]);

            $query = "select id from administrative_record where student_id = '".$studentId."'";
            $result = $this->connection->executeQuery($query);
            $administrativeRecordId = $result->fetchAssociative()['id'];

            $query = "select * from student_diploma where administrative_record_id = ".$administrativeRecordId;
            $result = $this->connection->executeQuery($query);
            $lastStudentDiploma = $result->fetchAssociative();

            // Add dualpath studenDiploma
            if ('1' === $row[1]) {
                $query = "
                insert into student_diploma
                    (
                         id,
                         diploma_channel_id,
                         diploma_id,
                         administrative_record_id,
                         year,
                         establishment,
                         postal_code,
                         city,
                         detail,
                         last_diploma
                    ) values 
                    (
                         nextval('student_diploma_id_seq'),
                        (select id from diploma_channel where name='Assurance' ),
                        (select id from diploma where name='BTS' ),
                        " . $administrativeRecordId . ",
                        " . (int)$lastStudentDiploma['year'] . ",
                        '" . str_replace("'", "''", $lastStudentDiploma['establishment']) . "',
                        '" . $lastStudentDiploma['postal_code'] . "',
                        '" . str_replace("'", "''", $lastStudentDiploma['city']) . "',
                        '" . str_replace("'", "''", $lastStudentDiploma['detail']) . "',
                        '1'     
                    )
                    returning id
                ";

                $dualStudentDiplomaResult = $this->connection->executeQuery($query);
                $dualPathStudentDiplomaId = $dualStudentDiplomaResult->fetchAssociative()['id'];

                $query = 'update student_diploma set dual_path_student_diploma_id = '.$dualPathStudentDiplomaId.' where id = ' . $lastStudentDiploma['id'];
                $this->connection->executeQuery($query);
            }

            $scholarShipLevel = $row[11] == '1' ? '1' : 'null';
            $query = "
                update administrative_record
                set exam_language_id = (select id from exam_language where name = '".$row[8]."'),
                high_level_sportsman = '".$row[9]."',
                sport_level_id = (select id from sport_level where label = '".$row[10]."'),
                scholar_ship = '".$row[11]."',
                scholar_ship_level_id = ".$scholarShipLevel.",
                third_time = '".$row[13]."'
                where id = ".$administrativeRecordId."
            ";
            $this->connection->executeQuery($query);

            if ($row[13] == '1') {
                $mediaId = $this->createMedia($studentId, 'tt');

                $query = "insert into 
                    third_time_medias
                    (
                        administrative_record_id,
                        media_id
                    ) values (
                        ".$administrativeRecordId.",
                        ".$mediaId."
                    )
                ";

                $this->connection->executeQuery($query);
            }

            if ($row[9] == '1') {
                $mediaId = $this->createMedia($studentId, 'shn');

                $query = "insert into 
                    high_level_sportsman_medias
                    (
                        administrative_record_id,
                        media_id
                    ) values (
                        ".$administrativeRecordId.",
                        ".$mediaId."
                    )
                ";

                $this->connection->executeQuery($query);
            }

            if ($row[11] == '1') {
                $mediaId = $this->createMedia($studentId, 'crous');;

                $query = "insert into scholar_ship_medias
                    (
                        administrative_record_id,
                        media_id
                    ) values (
                        ".$administrativeRecordId.",
                        ".$mediaId."
                    )
                ";

                $this->connection->executeQuery($query);
            }

            $i++;
        }
    }

    private function importCandidat(): void
    {
        $handle = fopen("data/import/init_candidat.csv", "r");

        $i = 0;
        while (($rawString = fgets($handle)) !== false) {
            $row = str_getcsv($rawString, ';');
            if (0 === $i) {
                $i++;
                continue;
            }

            $query = "insert into users  (
                    id, 
                    student_id, 
                    email, 
                    roles, 
                    plain_password, 
                    password, 
                    first_name, 
                    last_name, 
                    created_at, 
                    updated_at
                ) values (
                    uuid_generate_v4(),
                    null,
                    '".$row[0]."',
                    'a:1:{i:0;s:14:\"ROLE_CANDIDATE\";}',
                    null,
                    '$2y$13$8yeevGNZZoNBBvEJLBFZouOPFiWFZCTiVYvUuqXRvaUojcH3l3zpi',
                    '".str_replace("'","''",$row[3])."',
                    '".str_replace("'","''",$row[4])."',
                    now(),
                    now()
                )
                returning id
               ";


            $this->connection->executeQuery('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
            $this->connection->executeQuery('CREATE EXTENSION IF NOT EXISTS "unaccent";');
            $result = $this->connection->executeQuery($query);
            $userId = $result->fetchAssociative()['id'];

            $countryId = $this->getCountryId($row[6]);

            $query = "
                insert into student (
                    id,
                    program_channel_id,
                    country_id,
                    country_birth_id,
                    nationality_id,
                    nationality_secondary_id,
                    date_of_birth,
                    state,
                    transition,
                    first_name_secondary,
                    gender,
                    identifier,
                    phone,
                    address,
                    postal_code,
                    city,
                    competition_fees_payment,
                    created_at, 
                    updated_at
                ) values (
                    nextval('student_id_seq'),
                    (select id from program_channel where name = '".($row[1] == 'AST1' ? 'AST 1' : 'AST 2')."'),
                    ".$countryId.",
                    ".$countryId.",
                    ".$countryId.",
                    null,
                    '".$row[5]."',
                    'approved',
                    'eligible_to_approved',
                    null,
                    '".$row[2]."',
                    '".sprintf("%d%'.04d", substr((string)date('Y'), -2), $i)."',
                    '0102030405',
                    'mon adresse',
                    '59000',
                    'Lille',
                    '1',
                    now(),
                    now()
                ) returning id
            ";

            $result = $this->connection->executeQuery($query);
            $studentId = $result->fetchAssociative()['id'];

            $query = "update users set student_id = ".$studentId." where id = '".$userId."'";
            $this->connection->executeQuery($query);

            $query = "
                insert into administrative_record
                    (
                         id,
                         student_id
                    ) values (
                        nextval('administrative_record_id_seq'),
                        ".$studentId." 
                )
                returning id
            ";

            $result = $this->connection->executeQuery($query);
            $administrativeRecordId = $result->fetchAssociative()['id'];

            $query = "insert into cv 
                (
                 id, 
                 student_id,
                 validated
                ) values
                (
                 nextval('cv_id_seq'),
                 '".$studentId."',
                 '1'
                )
                returning id
            ";
            $cvResult = $this->connection->executeQuery($query);
            $this->cvIds[] = $cvResult->fetchAssociative()['id'];

            $row[11] = str_replace(' (nous contacter)', '', $row[11]);
            $row[12] = str_replace('Autre - Préciser', 'Autre', $row[12]);
            $row[12] = str_replace('COMMERCE/MARKETING', 'Commerce / marketing', $row[12]);
            $row[12] = str_replace('E COMMERCE MARKETING DIGITAL', 'E-Commerce Marketing Digital', $row[12]);
            $row[12] = str_replace('ÉCONOMIE/FINANCE', 'Finance', $row[12]);
            $row[12] = str_replace('Préciser l\'intitulé', 'Précisez l’intitulé', $row[12]);
            $row[12] = str_replace(' Autre', 'Autre', $row[12]);
            $query = "
                insert into student_diploma
                    (
                         id,
                         diploma_channel_id,
                         diploma_id,
                         administrative_record_id,
                         year,
                         establishment,
                         postal_code,
                         city,
                         detail,
                         last_diploma
                    ) values 
                    (
                         nextval('student_diploma_id_seq'),
                        (
                            select dc.id from diploma_channel dc
                            left join diploma_diploma_channel ddc  on ddc.diploma_channel_id = dc.id
                            left join diploma d on d.id = ddc.diploma_id
                            where upper(unaccent(dc.name)) = upper(unaccent('".str_replace("'","''",trim($row[12]))."')) and upper(unaccent(d.name)) = upper(unaccent('".str_replace("'","''",trim($row[11]))."'))
                        ),
                        (select id from diploma where upper(unaccent(name)) = upper(unaccent('".str_replace("'","''",trim($row[11]))."')) ),
                        ".$administrativeRecordId.",
                        ".(int) $row[14].",
                        '".str_replace("'","''",$row[15])."',
                        '".str_replace("'","''",$row[16])."',
                        '".str_replace("'","''",$row[17])."',
                        '".str_replace("'","''",$row[13])."',
                        '1'       
                    )
                    returning id
            ";

            $result = $this->connection->executeQuery($query);
            $lastDiplomaId = $result->fetchAssociative()['id'];

            $mediaId = $this->createMedia($studentId, 'certificat_eligibilite');;

            $query = "insert into student_diploma_media
                (
                    student_diploma_id,
                    media_id
                ) values (
                    ".$lastDiplomaId.",
                    ".$mediaId."
                )
            ";
            $this->connection->executeQuery($query);

            $i++;
        }

        fclose($handle);
    }

    private function dispatchCvCalculation(): void
    {
        foreach ($this->cvIds as $id) {
            $this->bus->dispatch(new CvCalculationMessage($id));
        }
    }
}