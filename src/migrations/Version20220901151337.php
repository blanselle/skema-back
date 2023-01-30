<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220901151337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ExamSummon entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE exam_summon_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exam_summon (id INT NOT NULL, media_id INT NOT NULL, student_id INT NOT NULL, exam_session_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_759AB63EA9FDD75 ON exam_summon (media_id)');
        $this->addSql('CREATE INDEX IDX_759AB63CB944F1A ON exam_summon (student_id)');
        $this->addSql('CREATE INDEX IDX_759AB637337968A ON exam_summon (exam_session_id)');
        $this->addSql('ALTER TABLE exam_summon ADD CONSTRAINT FK_759AB63EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_summon ADD CONSTRAINT FK_759AB63CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_summon ADD CONSTRAINT FK_759AB637337968A FOREIGN KEY (exam_session_id) REFERENCES exam_session (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE exam_summon_id_seq CASCADE');
        $this->addSql('DROP TABLE exam_summon');
    }
}
