<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221214141622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Oral test] Campus slot';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE campus_oral_day_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE campus_oral_day_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE campus_oral_day (id INT NOT NULL, configuration_id INT NOT NULL, first_language_id INT DEFAULT NULL, second_language_id INT DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nb_of_reserved_places INT NOT NULL, nb_of_available_places INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3EEEDAB73F32DD8 ON campus_oral_day (configuration_id)');
        $this->addSql('CREATE INDEX IDX_A3EEEDAB1C04B736 ON campus_oral_day (first_language_id)');
        $this->addSql('CREATE INDEX IDX_A3EEEDABEB1426EA ON campus_oral_day (second_language_id)');
        $this->addSql('COMMENT ON COLUMN campus_oral_day.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE campus_oral_day_configuration (id INT NOT NULL, campus_id INT NOT NULL, optional_lv1 BOOLEAN DEFAULT NULL, optional_lv2 BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2B13DAA6AF5D55E1 ON campus_oral_day_configuration (campus_id)');
        $this->addSql('CREATE TABLE campus_oral_day_configuration_program_channel (campus_oral_day_configuration_id INT NOT NULL, program_channel_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, program_channel_id))');
        $this->addSql('CREATE INDEX IDX_A5EEEA6C44DD8882 ON campus_oral_day_configuration_program_channel (campus_oral_day_configuration_id)');
        $this->addSql('CREATE INDEX IDX_A5EEEA6C64CF5C1E ON campus_oral_day_configuration_program_channel (program_channel_id)');
        $this->addSql('CREATE TABLE campus_oral_day_first_languages (campus_oral_day_configuration_id INT NOT NULL, exam_language_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, exam_language_id))');
        $this->addSql('CREATE INDEX IDX_AA4269DC44DD8882 ON campus_oral_day_first_languages (campus_oral_day_configuration_id)');
        $this->addSql('CREATE INDEX IDX_AA4269DC99E217F5 ON campus_oral_day_first_languages (exam_language_id)');
        $this->addSql('CREATE TABLE campus_oral_day_second_languages (campus_oral_day_configuration_id INT NOT NULL, exam_language_id INT NOT NULL, PRIMARY KEY(campus_oral_day_configuration_id, exam_language_id))');
        $this->addSql('CREATE INDEX IDX_AA1477AB44DD8882 ON campus_oral_day_second_languages (campus_oral_day_configuration_id)');
        $this->addSql('CREATE INDEX IDX_AA1477AB99E217F5 ON campus_oral_day_second_languages (exam_language_id)');
        $this->addSql('ALTER TABLE campus_oral_day ADD CONSTRAINT FK_A3EEEDAB73F32DD8 FOREIGN KEY (configuration_id) REFERENCES campus_oral_day_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day ADD CONSTRAINT FK_A3EEEDAB1C04B736 FOREIGN KEY (first_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day ADD CONSTRAINT FK_A3EEEDABEB1426EA FOREIGN KEY (second_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_configuration ADD CONSTRAINT FK_2B13DAA6AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel ADD CONSTRAINT FK_A5EEEA6C44DD8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel ADD CONSTRAINT FK_A5EEEA6C64CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages ADD CONSTRAINT FK_AA4269DC44DD8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages ADD CONSTRAINT FK_AA4269DC99E217F5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages ADD CONSTRAINT FK_AA1477AB44DD8882 FOREIGN KEY (campus_oral_day_configuration_id) REFERENCES campus_oral_day_configuration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages ADD CONSTRAINT FK_AA1477AB99E217F5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE campus_oral_day_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE campus_oral_day_configuration_id_seq CASCADE');
        $this->addSql('ALTER TABLE campus_oral_day DROP CONSTRAINT FK_A3EEEDAB73F32DD8');
        $this->addSql('ALTER TABLE campus_oral_day DROP CONSTRAINT FK_A3EEEDAB1C04B736');
        $this->addSql('ALTER TABLE campus_oral_day DROP CONSTRAINT FK_A3EEEDABEB1426EA');
        $this->addSql('ALTER TABLE campus_oral_day_configuration DROP CONSTRAINT FK_2B13DAA6AF5D55E1');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel DROP CONSTRAINT FK_A5EEEA6C44DD8882');
        $this->addSql('ALTER TABLE campus_oral_day_configuration_program_channel DROP CONSTRAINT FK_A5EEEA6C64CF5C1E');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages DROP CONSTRAINT FK_AA4269DC44DD8882');
        $this->addSql('ALTER TABLE campus_oral_day_first_languages DROP CONSTRAINT FK_AA4269DC99E217F5');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages DROP CONSTRAINT FK_AA1477AB44DD8882');
        $this->addSql('ALTER TABLE campus_oral_day_second_languages DROP CONSTRAINT FK_AA1477AB99E217F5');
        $this->addSql('DROP TABLE campus_oral_day');
        $this->addSql('DROP TABLE campus_oral_day_configuration');
        $this->addSql('DROP TABLE campus_oral_day_configuration_program_channel');
        $this->addSql('DROP TABLE campus_oral_day_first_languages');
        $this->addSql('DROP TABLE campus_oral_day_second_languages');
    }
}
