<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230118131046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change exam_student confirmed column type (boolean to integer) and get data casted';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_student ADD confirmed_new INT NOT NULL DEFAULT 0');
        $this->addSql('UPDATE exam_student SET confirmed_new = (
                                SELECT CASE
                                   WHEN es.confirmed = false THEN 0
                                   WHEN es.confirmed = true THEN 2
                                   END
                                FROM exam_student es
                                WHERE exam_student.id = es.id)
                                WHERE 1 = 1');
        $this->addSql('ALTER TABLE exam_student DROP confirmed');
        $this->addSql('ALTER TABLE exam_student RENAME COLUMN confirmed_new TO confirmed');
        $this->addSql('ALTER TABLE exam_student ALTER confirmed SET DEFAULT 0');
        $this->addSql('COMMENT ON COLUMN exam_student.confirmed IS \'0:aucune action côté BO, 1: annulée, 2: confirmée\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_student ADD confirmed_new BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('UPDATE exam_student SET confirmed_new = (
                                SELECT CASE
                                   WHEN es.confirmed = 0 THEN false
                                   WHEN es.confirmed = 2 THEN true
                                   END
                                FROM exam_student es
                                WHERE exam_student.id = es.id)
                                WHERE 1 = 1');
        $this->addSql('ALTER TABLE exam_student DROP COLUMN confirmed');
        $this->addSql('ALTER TABLE exam_student RENAME COLUMN confirmed_new TO confirmed');
        $this->addSql('ALTER TABLE exam_student ALTER confirmed DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN exam_student.confirmed IS NULL');
    }
}
