<?php

declare(strict_types=1);

namespace App\Helper;

use App\Exception\ResetDbException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DbHelper
{
    const REQUEST_ATTRIBUTES = [
        'exam_summon' => [],
        'bac' => [],
        'bac_sup' => [],
        'cv_language' => [],
        'cv' => [],
        'exam_student' => [],
        'oral_test_oral_test_student' => [],
        'payment' => [],
        'order' => [],
        'exam_session_exam_room' => [],
        'exam_session' => [],
        'exam_room' => [],
        'experience' => [],
        'high_level_sportsman_medias' => [],
        'id_card_medias' => [],
        'notification' => [],
        'student_diploma' => [],
        'student_diploma_media' => [],
        'student_workflow_history' => [],
        'oral_test_exam_period' => [],
        'oral_test_exam_test' => [],
        'oral_test_planning_info' => [],
        'third_time_medias' => [],
        'administrative_record' => [],
        'media' => ['student_id is not null'],
        'loggable_history' => [],
        'student' => [],
        'oral_test_jury' => [],
        'admissibility_calculator' => [],
        'admissibility_conversion_table' => [],
        'admissibility_border' => [],
        'admissibility_param' => [],
        'admissibility' => [],
        'refresh_tokens' => [],
        'oral_test_campus_oral_day' =>  [],
        'oral_test_campus_oral_day_configuration' =>  [],
        'oral_test_slot_configuration' =>  [],
        'oral_test_test_configuration' =>  [],
        'oral_test_campus_configuration' =>  [],
        'oral_test_sudoku_configuration' =>  [],
        'users' => ['student_id is not null']
    ];

    private Connection $con;

    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir
    ) {
        $this->con = $this->em->getConnection();
    }

    public function __destruct()
    {
        $this->con->close();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws ResetDbException
     */
    public function resetDb(): void
    {
        $this->deleteStudentMediasPhysically();
        $this->removeDataFromStudent();
        $this->removeSudokuConfigurationToProgramChannels();
        $this->resetTables();
    }

    /**
     * @throws ResetDbException
     */
    public function deleteStudentMediasPhysically(?int $studentId = null): void
    {
        try {
            if (null === $studentId) {
                $query = 'select * from media where student_id is not null';
            } else {
                $query = sprintf("select * from media where student_id = %d", $studentId);
            }

            $medias = $this->con->executeQuery($query)->fetchAllAssociative();
            foreach ($medias as $media) {
                $mediaPath = sprintf('%s/%s', $this->projectDir, $media['file']);
                if (file_exists($mediaPath)) {
                    unlink($mediaPath);
                }
            }
        } catch (\Exception $e) {
            throw new ResetDbException($e->getMessage());
        }
    }

    private function removeDataFromStudent(): void
    {
        try {
            $query = 'update student set english_note_used_id = null, management_note_used_id = null';
            $this->con->executeQuery($query);
        } catch (\Exception $e) {
            throw new ResetDbException($e->getMessage());
        }
    }

    /**
     * @throws ResetDbException
     */
    private function resetTables(): void
    {
        try {
            foreach (self::REQUEST_ATTRIBUTES as $table => $conditions) {
                $query = sprintf('delete from "%s"', $table);

                // reset sequence if exist
                if (empty($conditions)) {
                    $resetSeqQuery = sprintf(
                        "SELECT COUNT(*) FROM information_schema.sequences WHERE  sequence_name = '%s_id_seq'",
                        $table
                    );
                    $result = $this->con->executeQuery($resetSeqQuery);
                    $mixedOrFalse = $result->fetchAssociative();

                    if (
                        false !== $mixedOrFalse
                        && array_key_exists('count', $mixedOrFalse)
                        && $mixedOrFalse['count'] > 0
                    ) {
                        $resetSeqQuery = sprintf("SELECT setval('%s_id_seq', 1, true)", $table);
                        $this->con->executeQuery($resetSeqQuery);
                    }
                }

                if (!empty($conditions)) {
                    $firstLoop = true;
                    foreach ($conditions as $condition) {
                        if ($firstLoop) {
                            $query = sprintf('%s where %s', $query, $condition);
                            $firstLoop = false;
                            continue;
                        }

                        $query = sprintf('%s and %s', $query, $condition);
                    }
                }

                $this->con->executeQuery($query);
            }
        } catch (\Exception $e) {
            throw new ResetDbException($e->getMessage());
        }
    }

    private function removeSudokuConfigurationToProgramChannels(): void
    {
        try {
            $query = 'update program_channel set sudoku_configuration_id = null';
            $this->con->executeQuery($query);
        } catch (\Exception $e) {
            throw new ResetDbException($e->getMessage());
        }
    }
}