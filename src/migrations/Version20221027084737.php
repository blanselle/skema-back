<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221027084737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_summon ADD exam_student_id INT NOT NULL');
        $this->addSql('ALTER TABLE exam_summon ADD CONSTRAINT FK_759AB63D99C354F FOREIGN KEY (exam_student_id) REFERENCES exam_student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_759AB63D99C354F ON exam_summon (exam_student_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_summon DROP CONSTRAINT FK_759AB63D99C354F');
        $this->addSql('DROP INDEX UNIQ_759AB63D99C354F');
        $this->addSql('ALTER TABLE exam_summon DROP exam_student_id');
    }
}
