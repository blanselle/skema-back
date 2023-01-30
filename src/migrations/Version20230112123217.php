<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230112123217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Oral Test] - Sudoku initialization';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE sudoku_exam_period_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sudoku_exam_test_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sudoku_jury_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sudoku_planning_info_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE campus_oral_day_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE campus_oral_day_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_student_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE oral_test_campus_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_campus_oral_day_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_campus_oral_day_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_distribution_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_exam_period_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_exam_test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_jury_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_oral_test_student_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_planning_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_slot_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_slot_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_sudoku_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_test_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_test_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE oral_test_campus_configuration (id INT NOT NULL, distribution_id INT NOT NULL, campus_id INT DEFAULT NULL, sudoku_configuration_id INT DEFAULT NULL, minimum_duration_between_two_tests INT DEFAULT NULL, jury_debrief_duration INT DEFAULT NULL, preparation_room VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_19EE506B6EB6DDB5 ON oral_test_campus_configuration (distribution_id)');
        $this->addSql('CREATE INDEX IDX_19EE506BAF5D55E1 ON oral_test_campus_configuration (campus_id)');
        $this->addSql('CREATE INDEX IDX_19EE506B7FC833D8 ON oral_test_campus_configuration (sudoku_configuration_id)');
        $this->addSql('CREATE TABLE oral_test_campus_oral_day (id INT NOT NULL, configuration_id INT NOT NULL, first_language_id INT DEFAULT NULL, second_language_id INT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nb_of_reserved_places INT NOT NULL, nb_of_available_places INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64476E6073F32DD8 ON oral_test_campus_oral_day (configuration_id)');
        $this->addSql('CREATE INDEX IDX_64476E601C04B736 ON oral_test_campus_oral_day (first_language_id)');
        $this->addSql('CREATE INDEX IDX_64476E60EB1426EA ON oral_test_campus_oral_day (second_language_id)');
        $this->addSql('CREATE UNIQUE INDEX oral_test_campus_oral_day_unique_idx ON oral_test_campus_oral_day (configuration_id, first_language_id, second_language_id, date)');
        $this->addSql('COMMENT ON COLUMN oral_test_campus_oral_day.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE oral_test_campus_oral_day_configuration (id INT NOT NULL, campus_id INT NOT NULL, optional_lv1 BOOLEAN DEFAULT NULL, optional_lv2 BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D68C07FBAF5D55E1 ON oral_test_campus_oral_day_configuration (campus_id)');
        $this->addSql('CREATE TABLE oral_test_campus_oral_day_configuration_program_channel (campus_oral_day_configuration_id INT NOT NULL, program_channel_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, program_channel_id))');
        $this->addSql('CREATE INDEX IDX_B60A584144DD8882 ON oral_test_campus_oral_day_configuration_program_channel (campus_oral_day_configuration_id)');
        $this->addSql('CREATE INDEX IDX_B60A584164CF5C1E ON oral_test_campus_oral_day_configuration_program_channel (program_channel_id)');
        $this->addSql('CREATE TABLE oral_test_campus_oral_day_configuration_first_languages (campus_oral_day_configuration_id INT NOT NULL, exam_language_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, exam_language_id))');
        $this->addSql('CREATE INDEX IDX_C3C5EDE544DD8882 ON oral_test_campus_oral_day_configuration_first_languages (campus_oral_day_configuration_id)');
        $this->addSql('CREATE INDEX IDX_C3C5EDE599E217F5 ON oral_test_campus_oral_day_configuration_first_languages (exam_language_id)');
        $this->addSql('CREATE TABLE oral_test_campus_oral_day_configuration_second_languages (campus_oral_day_configuration_id INT NOT NULL, exam_language_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, exam_language_id))');
        $this->addSql('CREATE INDEX IDX_F578782744DD8882 ON oral_test_campus_oral_day_configuration_second_languages (campus_oral_day_configuration_id)');
        $this->addSql('CREATE INDEX IDX_F578782799E217F5 ON oral_test_campus_oral_day_configuration_second_languages (exam_language_id)');
        $this->addSql('CREATE TABLE oral_test_distribution_type (id INT NOT NULL, label VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, position INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN oral_test_distribution_type.code IS \'day or slot\'');
        $this->addSql('COMMENT ON COLUMN oral_test_distribution_type.position IS \'1: day, 2: slot\'');
        $this->addSql('CREATE TABLE oral_test_exam_period (id INT NOT NULL, exam_test_id INT NOT NULL, slot_type_id INT NOT NULL, nb_of_juries INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1DB3D3F513FB5D31 ON oral_test_exam_period (exam_test_id)');
        $this->addSql('CREATE INDEX IDX_1DB3D3F52B4AEE37 ON oral_test_exam_period (slot_type_id)');
        $this->addSql('CREATE TABLE oral_test_exam_test (id INT NOT NULL, planning_info_id INT NOT NULL, exam_language_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_882BC94980E69C13 ON oral_test_exam_test (planning_info_id)');
        $this->addSql('CREATE INDEX IDX_882BC94999E217F5 ON oral_test_exam_test (exam_language_id)');
        $this->addSql('CREATE TABLE oral_test_jury (id INT NOT NULL, exam_period_id INT NOT NULL, code VARCHAR(50) NOT NULL, class_room_number VARCHAR(10) NOT NULL, examiners TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8039FCC753187F87 ON oral_test_jury (exam_period_id)');
        $this->addSql('COMMENT ON COLUMN oral_test_jury.examiners IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE oral_test_oral_test_student (id INT NOT NULL, campus_oral_day_id INT NOT NULL, student_id INT NOT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1B51C7751F0E35CC ON oral_test_oral_test_student (campus_oral_day_id)');
        $this->addSql('CREATE INDEX IDX_1B51C775CB944F1A ON oral_test_oral_test_student (student_id)');
        $this->addSql('CREATE INDEX oral_test_oral_test_student_state_idx ON oral_test_oral_test_student (state)');
        $this->addSql('CREATE UNIQUE INDEX oral_test_oral_test_student_unique_index ON oral_test_oral_test_student (campus_oral_day_id, student_id) WHERE (state <> \'rejected\')');
        $this->addSql('CREATE TABLE oral_test_planning_info (id INT NOT NULL, contest_jury_website_code VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN oral_test_planning_info.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE oral_test_slot_configuration (id INT NOT NULL, slot_type_id INT NOT NULL, test_configuration_id INT NOT NULL, start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, break_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, break_duration INT DEFAULT NULL, nb_of_candidates_per_jury INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_60D4D9D62B4AEE37 ON oral_test_slot_configuration (slot_type_id)');
        $this->addSql('CREATE INDEX IDX_60D4D9D6753B56F7 ON oral_test_slot_configuration (test_configuration_id)');
        $this->addSql('COMMENT ON COLUMN oral_test_slot_configuration.start_time IS \'(DC2Type:time_immutable)\'');
        $this->addSql('COMMENT ON COLUMN oral_test_slot_configuration.end_time IS \'(DC2Type:time_immutable)\'');
        $this->addSql('COMMENT ON COLUMN oral_test_slot_configuration.break_time IS \'(DC2Type:time_immutable)\'');
        $this->addSql('CREATE TABLE oral_test_slot_type (id INT NOT NULL, label VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, position INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN oral_test_slot_type.code IS \'M morning or A afternoon or S evening\'');
        $this->addSql('COMMENT ON COLUMN oral_test_slot_type.position IS \'1: M, 2: A, 3: S\'');
        $this->addSql('CREATE TABLE oral_test_sudoku_configuration (id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE oral_test_test_configuration (id INT NOT NULL, test_type_id INT NOT NULL, campus_configuration_id INT NOT NULL, duration_of_test INT NOT NULL, preparation_time INT DEFAULT NULL, evening_event BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A149BA39190D585B ON oral_test_test_configuration (test_type_id)');
        $this->addSql('CREATE INDEX IDX_A149BA3924DEAC20 ON oral_test_test_configuration (campus_configuration_id)');
        $this->addSql('CREATE TABLE oral_test_test_type (id INT NOT NULL, label VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, position INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN oral_test_test_type.code IS \'ent or lang\'');
        $this->addSql('COMMENT ON COLUMN oral_test_test_type.position IS \'1: ent, 2: lang\'');
        $this->addSql('ALTER TABLE oral_test_campus_configuration ADD CONSTRAINT FK_19EE506B6EB6DDB5 FOREIGN KEY (distribution_id) REFERENCES oral_test_distribution_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_configuration ADD CONSTRAINT FK_19EE506BAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_configuration ADD CONSTRAINT FK_19EE506B7FC833D8 FOREIGN KEY (sudoku_configuration_id) REFERENCES oral_test_sudoku_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day ADD CONSTRAINT FK_64476E6073F32DD8 FOREIGN KEY (configuration_id) REFERENCES oral_test_campus_oral_day_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day ADD CONSTRAINT FK_64476E601C04B736 FOREIGN KEY (first_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day ADD CONSTRAINT FK_64476E60EB1426EA FOREIGN KEY (second_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration ADD CONSTRAINT FK_D68C07FBAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_program_channel ADD CONSTRAINT FK_B60A584144DD8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES oral_test_campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_program_channel ADD CONSTRAINT FK_B60A584164CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_first_languages ADD CONSTRAINT FK_C3C5EDE544DD8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES oral_test_campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_first_languages ADD CONSTRAINT FK_C3C5EDE599E217F5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_second_languages ADD CONSTRAINT FK_F578782744DD8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES oral_test_campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_second_languages ADD CONSTRAINT FK_F578782799E217F5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_exam_period ADD CONSTRAINT FK_1DB3D3F513FB5D31 FOREIGN KEY (exam_test_id) REFERENCES oral_test_exam_test (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_exam_period ADD CONSTRAINT FK_1DB3D3F52B4AEE37 FOREIGN KEY (slot_type_id) REFERENCES oral_test_slot_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_exam_test ADD CONSTRAINT FK_882BC94980E69C13 FOREIGN KEY (planning_info_id) REFERENCES oral_test_planning_info (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_exam_test ADD CONSTRAINT FK_882BC94999E217F5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_jury ADD CONSTRAINT FK_8039FCC753187F87 FOREIGN KEY (exam_period_id) REFERENCES oral_test_exam_period (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_oral_test_student ADD CONSTRAINT FK_1B51C7751F0E35CC FOREIGN KEY (campus_oral_day_id) REFERENCES oral_test_campus_oral_day (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_oral_test_student ADD CONSTRAINT FK_1B51C775CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_slot_configuration ADD CONSTRAINT FK_60D4D9D62B4AEE37 FOREIGN KEY (slot_type_id) REFERENCES oral_test_slot_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_slot_configuration ADD CONSTRAINT FK_60D4D9D6753B56F7 FOREIGN KEY (test_configuration_id) REFERENCES oral_test_test_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_test_configuration ADD CONSTRAINT FK_A149BA39190D585B FOREIGN KEY (test_type_id) REFERENCES oral_test_test_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_test_configuration ADD CONSTRAINT FK_A149BA3924DEAC20 FOREIGN KEY (campus_configuration_id) REFERENCES oral_test_campus_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sudoku_exam_period DROP CONSTRAINT fk_60f4a46013fb5d31');
        $this->addSql('ALTER TABLE sudoku_exam_test DROP CONSTRAINT fk_be0420a380e69c13');
        $this->addSql('ALTER TABLE sudoku_jury DROP CONSTRAINT fk_4e0f1a3a53187f87');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages DROP CONSTRAINT fk_aa1477ab44dd8882');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages DROP CONSTRAINT fk_aa1477ab99e217f5');
        $this->addSql('ALTER TABLE campus_oral_day_configuration DROP CONSTRAINT fk_2b13daa6af5d55e1');
        $this->addSql('ALTER TABLE oral_test_student DROP CONSTRAINT fk_47eedcc31f0e35cc');
        $this->addSql('ALTER TABLE oral_test_student DROP CONSTRAINT fk_47eedcc3cb944f1a');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel DROP CONSTRAINT fk_a5eeea6c44dd8882');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel DROP CONSTRAINT fk_a5eeea6c64cf5c1e');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages DROP CONSTRAINT fk_aa4269dc44dd8882');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages DROP CONSTRAINT fk_aa4269dc99e217f5');
        $this->addSql('ALTER TABLE campus_oral_day DROP CONSTRAINT fk_a3eeedab73f32dd8');
        $this->addSql('ALTER TABLE campus_oral_day DROP CONSTRAINT fk_a3eeedab1c04b736');
        $this->addSql('ALTER TABLE campus_oral_day DROP CONSTRAINT fk_a3eeedabeb1426ea');
        $this->addSql('DROP TABLE sudoku_planning_info');
        $this->addSql('DROP TABLE sudoku_exam_period');
        $this->addSql('DROP TABLE sudoku_exam_test');
        $this->addSql('DROP TABLE sudoku_jury');
        $this->addSql('DROP TABLE campus_oral_day_second_languages');
        $this->addSql('DROP TABLE campus_oral_day_configuration');
        $this->addSql('DROP TABLE oral_test_student');
        $this->addSql('DROP TABLE campus_oral_day_configuration_program_channel');
        $this->addSql('DROP TABLE campus_oral_day_first_languages');
        $this->addSql('DROP TABLE campus_oral_day');
        $this->addSql('ALTER TABLE program_channel ADD sudoku_configuration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program_channel ADD CONSTRAINT FK_291376937FC833D8 FOREIGN KEY (sudoku_configuration_id) REFERENCES oral_test_sudoku_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_291376937FC833D8 ON program_channel (sudoku_configuration_id)');

        // Insert data
        $this->addSql("INSERT INTO oral_test_slot_type (id, label, code, position , created_at, updated_at) 
        VALUES(nextval('oral_test_slot_type_id_seq'), 'Matin', 'M', 1, now(), now())");
        $this->addSql("INSERT INTO oral_test_slot_type (id, label, code, position , created_at, updated_at) 
        VALUES(nextval('oral_test_slot_type_id_seq'), 'Après-midi', 'A', 2, now(), now())");
        $this->addSql("INSERT INTO oral_test_slot_type (id, label, code, position , created_at, updated_at) 
        VALUES(nextval('oral_test_slot_type_id_seq'), 'Soirée', 'S', 3, now(), now())");

        $this->addSql("INSERT INTO oral_test_distribution_type (id, label, code, position , created_at, updated_at) 
        VALUES(nextval('oral_test_distribution_type_id_seq'), 'Journée', 'day', 1, now(), now())");
        $this->addSql("INSERT INTO oral_test_distribution_type (id, label, code, position , created_at, updated_at) 
        VALUES(nextval('oral_test_distribution_type_id_seq'), 'Slot', 'slot', 2, now(), now())");

        $this->addSql("INSERT INTO oral_test_test_type (id, label, code, position , created_at, updated_at) 
        VALUES(nextval('oral_test_test_type_id_seq'), 'Entretien', 'ent', 1, now(), now())");
        $this->addSql("INSERT INTO oral_test_test_type (id, label, code, position , created_at, updated_at) 
        VALUES(nextval('oral_test_test_type_id_seq'), 'Langue vivante', 'lang', 2, now(), now())");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE program_channel DROP CONSTRAINT FK_291376937FC833D8');
        $this->addSql('DROP SEQUENCE oral_test_campus_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_campus_oral_day_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_campus_oral_day_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_distribution_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_exam_period_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_exam_test_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_jury_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_oral_test_student_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_planning_info_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_slot_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_slot_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_sudoku_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_test_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE oral_test_test_type_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE sudoku_exam_period_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sudoku_exam_test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sudoku_jury_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sudoku_planning_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE campus_oral_day_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE campus_oral_day_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE oral_test_student_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sudoku_planning_info (id INT NOT NULL, contest_jury_website_code VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sudoku_planning_info.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sudoku_exam_period (id INT NOT NULL, exam_test_id INT NOT NULL, period VARCHAR(1) NOT NULL, nb_of_juries INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_60f4a46013fb5d31 ON sudoku_exam_period (exam_test_id)');
        $this->addSql('CREATE TABLE sudoku_exam_test (id INT NOT NULL, planning_info_id INT NOT NULL, language_code VARCHAR(5) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_be0420a380e69c13 ON sudoku_exam_test (planning_info_id)');
        $this->addSql('CREATE TABLE sudoku_jury (id INT NOT NULL, exam_period_id INT NOT NULL, code VARCHAR(50) NOT NULL, class_room_number VARCHAR(10) NOT NULL, examiners TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_4e0f1a3a53187f87 ON sudoku_jury (exam_period_id)');
        $this->addSql('COMMENT ON COLUMN sudoku_jury.examiners IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE campus_oral_day_second_languages (campus_oral_day_configuration_id INT NOT NULL, exam_language_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, exam_language_id))');
        $this->addSql('CREATE INDEX idx_aa1477ab99e217f5 ON campus_oral_day_second_languages (exam_language_id)');
        $this->addSql('CREATE INDEX idx_aa1477ab44dd8882 ON campus_oral_day_second_languages (campus_oral_day_configuration_id)');
        $this->addSql('CREATE TABLE campus_oral_day_configuration (id INT NOT NULL, campus_id INT NOT NULL, optional_lv1 BOOLEAN DEFAULT NULL, optional_lv2 BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2b13daa6af5d55e1 ON campus_oral_day_configuration (campus_id)');
        $this->addSql('CREATE TABLE oral_test_student (id INT NOT NULL, campus_oral_day_id INT NOT NULL, student_id INT NOT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX oral_test_student_state_idx ON oral_test_student (state)');
        $this->addSql('CREATE UNIQUE INDEX oral_test_student_unique_index ON oral_test_student (campus_oral_day_id, student_id) WHERE ((state)::text <> \'rejected\'::text)');
        $this->addSql('CREATE INDEX idx_47eedcc3cb944f1a ON oral_test_student (student_id)');
        $this->addSql('CREATE INDEX idx_47eedcc31f0e35cc ON oral_test_student (campus_oral_day_id)');
        $this->addSql('CREATE TABLE campus_oral_day_configuration_program_channel (campus_oral_day_configuration_id INT NOT NULL, program_channel_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, program_channel_id))');
        $this->addSql('CREATE INDEX idx_a5eeea6c64cf5c1e ON campus_oral_day_configuration_program_channel (program_channel_id)');
        $this->addSql('CREATE INDEX idx_a5eeea6c44dd8882 ON campus_oral_day_configuration_program_channel (campus_oral_day_configuration_id)');
        $this->addSql('CREATE TABLE campus_oral_day_first_languages (campus_oral_day_configuration_id INT NOT NULL, exam_language_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, exam_language_id))');
        $this->addSql('CREATE INDEX idx_aa4269dc99e217f5 ON campus_oral_day_first_languages (exam_language_id)');
        $this->addSql('CREATE INDEX idx_aa4269dc44dd8882 ON campus_oral_day_first_languages (campus_oral_day_configuration_id)');
        $this->addSql('CREATE TABLE campus_oral_day (id INT NOT NULL, configuration_id INT NOT NULL, first_language_id INT DEFAULT NULL, second_language_id INT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nb_of_reserved_places INT NOT NULL, nb_of_available_places INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX oral_test_campus_oral_day_unique_index ON campus_oral_day (configuration_id, first_language_id, second_language_id, date)');
        $this->addSql('CREATE INDEX idx_a3eeedabeb1426ea ON campus_oral_day (second_language_id)');
        $this->addSql('CREATE INDEX idx_a3eeedab1c04b736 ON campus_oral_day (first_language_id)');
        $this->addSql('CREATE INDEX idx_a3eeedab73f32dd8 ON campus_oral_day (configuration_id)');
        $this->addSql('COMMENT ON COLUMN campus_oral_day.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sudoku_exam_period ADD CONSTRAINT fk_60f4a46013fb5d31 FOREIGN KEY (exam_test_id) REFERENCES sudoku_exam_test (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sudoku_exam_test ADD CONSTRAINT fk_be0420a380e69c13 FOREIGN KEY (planning_info_id) REFERENCES sudoku_planning_info (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sudoku_jury ADD CONSTRAINT fk_4e0f1a3a53187f87 FOREIGN KEY (exam_period_id) REFERENCES sudoku_exam_period (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages ADD CONSTRAINT fk_aa1477ab44dd8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages ADD CONSTRAINT fk_aa1477ab99e217f5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_configuration ADD CONSTRAINT fk_2b13daa6af5d55e1 FOREIGN KEY (campus_id) REFERENCES campus (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_student ADD CONSTRAINT fk_47eedcc31f0e35cc FOREIGN KEY (campus_oral_day_id) REFERENCES campus_oral_day (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_student ADD CONSTRAINT fk_47eedcc3cb944f1a FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel ADD CONSTRAINT fk_a5eeea6c44dd8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel ADD CONSTRAINT fk_a5eeea6c64cf5c1e FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages ADD CONSTRAINT fk_aa4269dc44dd8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages ADD CONSTRAINT fk_aa4269dc99e217f5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day ADD CONSTRAINT fk_a3eeedab73f32dd8 FOREIGN KEY (configuration_id) REFERENCES campus_oral_day_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day ADD CONSTRAINT fk_a3eeedab1c04b736 FOREIGN KEY (first_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day ADD CONSTRAINT fk_a3eeedabeb1426ea FOREIGN KEY (second_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_campus_configuration DROP CONSTRAINT FK_19EE506B6EB6DDB5');
        $this->addSql('ALTER TABLE oral_test_campus_configuration DROP CONSTRAINT FK_19EE506BAF5D55E1');
        $this->addSql('ALTER TABLE oral_test_campus_configuration DROP CONSTRAINT FK_19EE506B7FC833D8');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day DROP CONSTRAINT FK_64476E6073F32DD8');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day DROP CONSTRAINT FK_64476E601C04B736');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day DROP CONSTRAINT FK_64476E60EB1426EA');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration DROP CONSTRAINT FK_D68C07FBAF5D55E1');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_program_channel DROP CONSTRAINT FK_B60A584144DD8882');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_program_channel DROP CONSTRAINT FK_B60A584164CF5C1E');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_first_languages DROP CONSTRAINT FK_C3C5EDE544DD8882');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_first_languages DROP CONSTRAINT FK_C3C5EDE599E217F5');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_second_languages DROP CONSTRAINT FK_F578782744DD8882');
        $this->addSql('ALTER TABLE oral_test_campus_oral_day_configuration_second_languages DROP CONSTRAINT FK_F578782799E217F5');
        $this->addSql('ALTER TABLE oral_test_exam_period DROP CONSTRAINT FK_1DB3D3F513FB5D31');
        $this->addSql('ALTER TABLE oral_test_exam_period DROP CONSTRAINT FK_1DB3D3F52B4AEE37');
        $this->addSql('ALTER TABLE oral_test_exam_test DROP CONSTRAINT FK_882BC94980E69C13');
        $this->addSql('ALTER TABLE oral_test_exam_test DROP CONSTRAINT FK_882BC94999E217F5');
        $this->addSql('ALTER TABLE oral_test_jury DROP CONSTRAINT FK_8039FCC753187F87');
        $this->addSql('ALTER TABLE oral_test_oral_test_student DROP CONSTRAINT FK_1B51C7751F0E35CC');
        $this->addSql('ALTER TABLE oral_test_oral_test_student DROP CONSTRAINT FK_1B51C775CB944F1A');
        $this->addSql('ALTER TABLE oral_test_slot_configuration DROP CONSTRAINT FK_60D4D9D62B4AEE37');
        $this->addSql('ALTER TABLE oral_test_slot_configuration DROP CONSTRAINT FK_60D4D9D6753B56F7');
        $this->addSql('ALTER TABLE oral_test_test_configuration DROP CONSTRAINT FK_A149BA39190D585B');
        $this->addSql('ALTER TABLE oral_test_test_configuration DROP CONSTRAINT FK_A149BA3924DEAC20');
        $this->addSql('DROP TABLE oral_test_campus_configuration');
        $this->addSql('DROP TABLE oral_test_campus_oral_day');
        $this->addSql('DROP TABLE oral_test_campus_oral_day_configuration');
        $this->addSql('DROP TABLE oral_test_campus_oral_day_configuration_program_channel');
        $this->addSql('DROP TABLE oral_test_campus_oral_day_configuration_first_languages');
        $this->addSql('DROP TABLE oral_test_campus_oral_day_configuration_second_languages');
        $this->addSql('DROP TABLE oral_test_distribution_type');
        $this->addSql('DROP TABLE oral_test_exam_period');
        $this->addSql('DROP TABLE oral_test_exam_test');
        $this->addSql('DROP TABLE oral_test_jury');
        $this->addSql('DROP TABLE oral_test_oral_test_student');
        $this->addSql('DROP TABLE oral_test_planning_info');
        $this->addSql('DROP TABLE oral_test_slot_configuration');
        $this->addSql('DROP TABLE oral_test_slot_type');
        $this->addSql('DROP TABLE oral_test_sudoku_configuration');
        $this->addSql('DROP TABLE oral_test_test_configuration');
        $this->addSql('DROP TABLE oral_test_test_type');
        $this->addSql('DROP INDEX IDX_291376937FC833D8');
        $this->addSql('ALTER TABLE program_channel DROP sudoku_configuration_id');
    }
}
