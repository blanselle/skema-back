<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220812091623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change table name to add admissibility domain + new field student';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE coefficient_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE admissibility_coefficient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_coefficient (id INT NOT NULL, program_channel_id INT NOT NULL, type VARCHAR(50) NOT NULL, coefficient INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8E908F0D64CF5C1E ON admissibility_coefficient (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_coefficient ADD CONSTRAINT FK_8E908F0D64CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE coefficient');
        $this->addSql('ALTER TABLE student ADD admissibility_global_score DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE admissibility_coefficient_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE coefficient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE coefficient (id INT NOT NULL, program_channel_id INT NOT NULL, type VARCHAR(50) NOT NULL, coefficient INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_3f061b6164cf5c1e ON coefficient (program_channel_id)');
        $this->addSql('ALTER TABLE coefficient ADD CONSTRAINT fk_3f061b6164cf5c1e FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE admissibility_coefficient');
        $this->addSql('ALTER TABLE student DROP admissibility_global_score');
    }
}
