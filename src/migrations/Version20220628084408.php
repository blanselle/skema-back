<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628084408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE exam_classification_score_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exam_classification_score (id INT NOT NULL, exam_classification_id INT NOT NULL, score DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D08EC03BB269FD ON exam_classification_score (exam_classification_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D08EC03BB269FD32993751 ON exam_classification_score (exam_classification_id, score)');
        $this->addSql('ALTER TABLE exam_classification_score ADD CONSTRAINT FK_7D08EC03BB269FD FOREIGN KEY (exam_classification_id) REFERENCES exam_classification (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_session ALTER distributed DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE exam_classification_score_id_seq CASCADE');
        $this->addSql('DROP TABLE exam_classification_score');
        $this->addSql('ALTER TABLE exam_session ALTER distributed SET DEFAULT false');
    }
}
