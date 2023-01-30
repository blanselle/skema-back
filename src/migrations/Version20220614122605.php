<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220614122605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ExamStudent new fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_student ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_student ADD score INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_student ADD absent BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_student ADD paid BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_student ADD CONSTRAINT FK_BA85F65BEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA85F65BEA9FDD75 ON exam_student (media_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_student DROP CONSTRAINT FK_BA85F65BEA9FDD75');
        $this->addSql('DROP INDEX UNIQ_BA85F65BEA9FDD75');
        $this->addSql('ALTER TABLE exam_student DROP media_id');
        $this->addSql('ALTER TABLE exam_student DROP score');
        $this->addSql('ALTER TABLE exam_student DROP absent');
        $this->addSql('ALTER TABLE exam_student DROP paid');
    }
}
