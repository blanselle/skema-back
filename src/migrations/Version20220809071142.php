<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220809071142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ranking entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE coefficient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE coefficient (id INT NOT NULL, program_channel_id INT NOT NULL, type VARCHAR(50) NOT NULL, coefficient INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3F061B6164CF5C1E ON coefficient (program_channel_id)');
        $this->addSql('ALTER TABLE coefficient ADD CONSTRAINT FK_3F061B6164CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD admissibility_global_note DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD admissibility_ranking INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_student ALTER admissibility_note TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE exam_student ALTER admissibility_note DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE student DROP admissibility_global_note');
        $this->addSql('ALTER TABLE student DROP admissibility_ranking');
        $this->addSql('DROP SEQUENCE coefficient_id_seq CASCADE');
        $this->addSql('DROP TABLE coefficient');
        $this->addSql('ALTER TABLE exam_student ALTER admissibility_note TYPE INT');
        $this->addSql('ALTER TABLE exam_student ALTER admissibility_note DROP DEFAULT');
    }
}
