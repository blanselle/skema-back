<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230103144735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE oral_test_student_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE oral_test_student (id INT NOT NULL, campus_oral_day_id INT NOT NULL, student_id INT NOT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_47EEDCC31F0E35CC ON oral_test_student (campus_oral_day_id)');
        $this->addSql('CREATE INDEX IDX_47EEDCC3CB944F1A ON oral_test_student (student_id)');
        $this->addSql('CREATE UNIQUE INDEX oral_test_student_unique_index ON oral_test_student (campus_oral_day_id, student_id) WHERE (state <> \'rejected\')');
        $this->addSql('ALTER TABLE oral_test_student ADD CONSTRAINT FK_47EEDCC31F0E35CC FOREIGN KEY (campus_oral_day_id) REFERENCES campus_oral_day (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oral_test_student ADD CONSTRAINT FK_47EEDCC3CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE oral_test_student_id_seq CASCADE');
        $this->addSql('ALTER TABLE oral_test_student DROP CONSTRAINT FK_47EEDCC31F0E35CC');
        $this->addSql('ALTER TABLE oral_test_student DROP CONSTRAINT FK_47EEDCC3CB944F1A');
        $this->addSql('DROP TABLE oral_test_student');
    }
}
