<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830114601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE sudoku_exam_period_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sudoku_exam_test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sudoku_jury_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sudoku_planning_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sudoku_exam_period (id INT NOT NULL, exam_test_id INT NOT NULL, period VARCHAR(1) NOT NULL, nb_of_juries INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_60F4A46013FB5D31 ON sudoku_exam_period (exam_test_id)');
        $this->addSql('CREATE TABLE sudoku_exam_test (id INT NOT NULL, planning_info_id INT NOT NULL, language_code VARCHAR(5) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BE0420A380E69C13 ON sudoku_exam_test (planning_info_id)');
        $this->addSql('CREATE TABLE sudoku_jury (id INT NOT NULL, exam_period_id INT NOT NULL, code VARCHAR(50) NOT NULL, class_room_number VARCHAR(10) NOT NULL, examiners TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4E0F1A3A53187F87 ON sudoku_jury (exam_period_id)');
        $this->addSql('COMMENT ON COLUMN sudoku_jury.examiners IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE sudoku_planning_info (id INT NOT NULL, contest_jury_website_code VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sudoku_planning_info.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sudoku_exam_period ADD CONSTRAINT FK_60F4A46013FB5D31 FOREIGN KEY (exam_test_id) REFERENCES sudoku_exam_test (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sudoku_exam_test ADD CONSTRAINT FK_BE0420A380E69C13 FOREIGN KEY (planning_info_id) REFERENCES sudoku_planning_info (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sudoku_jury ADD CONSTRAINT FK_4E0F1A3A53187F87 FOREIGN KEY (exam_period_id) REFERENCES sudoku_exam_period (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sudoku_jury DROP CONSTRAINT FK_4E0F1A3A53187F87');
        $this->addSql('ALTER TABLE sudoku_exam_period DROP CONSTRAINT FK_60F4A46013FB5D31');
        $this->addSql('ALTER TABLE sudoku_exam_test DROP CONSTRAINT FK_BE0420A380E69C13');
        $this->addSql('DROP SEQUENCE sudoku_exam_period_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sudoku_exam_test_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sudoku_jury_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sudoku_planning_info_id_seq CASCADE');
        $this->addSql('DROP TABLE sudoku_exam_period');
        $this->addSql('DROP TABLE sudoku_exam_test');
        $this->addSql('DROP TABLE sudoku_jury');
        $this->addSql('DROP TABLE sudoku_planning_info');
    }
}
