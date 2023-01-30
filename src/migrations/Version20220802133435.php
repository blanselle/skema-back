<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220802133435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Admissibility entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE admissibility_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE admissibility_border_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE admissibility_conversion_table_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE admissibility_param_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility (id INT NOT NULL, exam_classification_id INT NOT NULL, type VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_314AF0ED3BB269FD ON admissibility (exam_classification_id)');
        $this->addSql('CREATE TABLE admissibility_border (id INT NOT NULL, param_id INT NOT NULL, score DOUBLE PRECISION NOT NULL, note DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_13F78D165647C863 ON admissibility_border (param_id)');
        $this->addSql('CREATE TABLE admissibility_conversion_table (id INT NOT NULL, param_id INT NOT NULL, score DOUBLE PRECISION NOT NULL, note DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_39E384325647C863 ON admissibility_conversion_table (param_id)');
        $this->addSql('CREATE TABLE admissibility_param (id INT NOT NULL, program_channel_id INT NOT NULL, admissibility_id INT NOT NULL, high_clipping INT DEFAULT NULL, low_clipping INT DEFAULT NULL, median DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1D2CFFEA64CF5C1E ON admissibility_param (program_channel_id)');
        $this->addSql('CREATE INDEX IDX_1D2CFFEA45CECFC1 ON admissibility_param (admissibility_id)');
        $this->addSql('ALTER TABLE admissibility ADD CONSTRAINT FK_314AF0ED3BB269FD FOREIGN KEY (exam_classification_id) REFERENCES exam_classification (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_border ADD CONSTRAINT FK_13F78D165647C863 FOREIGN KEY (param_id) REFERENCES admissibility_param (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_conversion_table ADD CONSTRAINT FK_39E384325647C863 FOREIGN KEY (param_id) REFERENCES admissibility_param (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_param ADD CONSTRAINT FK_1D2CFFEA64CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_param ADD CONSTRAINT FK_1D2CFFEA45CECFC1 FOREIGN KEY (admissibility_id) REFERENCES admissibility (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_param DROP CONSTRAINT FK_1D2CFFEA45CECFC1');
        $this->addSql('ALTER TABLE admissibility_border DROP CONSTRAINT FK_13F78D165647C863');
        $this->addSql('ALTER TABLE admissibility_conversion_table DROP CONSTRAINT FK_39E384325647C863');
        $this->addSql('DROP SEQUENCE admissibility_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE admissibility_border_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE admissibility_conversion_table_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE admissibility_param_id_seq CASCADE');
        $this->addSql('DROP TABLE admissibility');
        $this->addSql('DROP TABLE admissibility_border');
        $this->addSql('DROP TABLE admissibility_conversion_table');
        $this->addSql('DROP TABLE admissibility_param');
    }
}
