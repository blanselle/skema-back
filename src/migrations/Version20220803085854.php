<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220803085854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMA] Add bonuses tables';
    }

    public function up(Schema $schema): void
    {
        // Bac Type
        $this->addSql('CREATE SEQUENCE admissibility_bonus_bac_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_bonus_bac_type (id INT NOT NULL, bac_type_id INT NOT NULL, program_channel_id INT NOT NULL, name VARCHAR(150) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_32CA65ECD2FEBD ON admissibility_bonus_bac_type (bac_type_id)');
        $this->addSql('CREATE INDEX IDX_32CA6564CF5C1E ON admissibility_bonus_bac_type (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type ADD CONSTRAINT FK_FC0E4BF34A986F5E FOREIGN KEY (bac_type_id) REFERENCES bac_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type ADD CONSTRAINT FK_FC0E4BF364CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Bac Distinction
        $this->addSql('CREATE SEQUENCE admissibility_bonus_distinction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_bonus_distinction (id INT NOT NULL, distinction VARCHAR(150) NOT NULL, program_channel_id INT NOT NULL, name VARCHAR(150) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6377D14664CF5C1E ON admissibility_bonus_distinction (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD CONSTRAINT FK_FC0E4BF364CF5C1F FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Sport Level
        $this->addSql('CREATE SEQUENCE admissibility_bonus_sport_level_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_bonus_sport_level (id INT NOT NULL, sport_level_id INT NOT NULL, program_channel_id INT NOT NULL, name VARCHAR(150) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_13DBF1086913B41D ON admissibility_bonus_sport_level (sport_level_id)');
        $this->addSql('CREATE INDEX IDX_13DBF10864CF5C1E ON admissibility_bonus_sport_level (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level ADD CONSTRAINT FK_96743E356913B41D FOREIGN KEY (sport_level_id) REFERENCES sport_level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level ADD CONSTRAINT FK_96743E3564CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Basic bonuses
        $this->addSql('CREATE SEQUENCE admissibility_bonus_basic_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_bonus_basic (id INT NOT NULL, program_channel_id INT NOT NULL, name VARCHAR(150) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F457629C64CF5C1E ON admissibility_bonus_basic (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_basic ADD CONSTRAINT FK_45ACD1E664CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Language
        $this->addSql('CREATE SEQUENCE admissibility_bonus_language_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_bonus_language (id INT NOT NULL, program_channel_id INT NOT NULL, name VARCHAR(150) NOT NULL, value DOUBLE PRECISION NOT NULL, min INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_977D29F564CF5C1E ON admissibility_bonus_language (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_language ADD CONSTRAINT FK_E0C07B2B64CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Experience
        $this->addSql('CREATE SEQUENCE admissibility_bonus_experience_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_bonus_experience (id INT NOT NULL, program_channel_id INT NOT NULL, type VARCHAR(150) NOT NULL, duration INT DEFAULT NULL, name VARCHAR(150) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9059146664CF5C1E ON admissibility_bonus_experience (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_experience ADD CONSTRAINT FK_FCCA036C64CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
    
    public function down(Schema $schema): void
    {
        // Bac Type
        $this->addSql('DROP SEQUENCE admissibility_bonus_bac_type_id_seq CASCADE');
        $this->addSql('DROP TABLE admissibility_bonus_bac_type');

        // Bac Distinction
        $this->addSql('DROP SEQUENCE admissibility_bonus_distinction_id_seq CASCADE');
        $this->addSql('DROP TABLE bac_distincion_bonus');

        // Sport Level
        $this->addSql('DROP SEQUENCE admissibility_bonus_sport_level_id_seq CASCADE');
        $this->addSql('DROP TABLE admissibility_bonus_sport_level');

        // Basic bonuses
        $this->addSql('DROP SEQUENCE admissibility_bonus_basic_id_seq CASCADE');
        $this->addSql('DROP TABLE admissibility_bonus_basic');

        // Language
        $this->addSql('DROP SEQUENCE admissibility_bonus_language_id_seq CASCADE');
        $this->addSql('DROP TABLE admissibility_bonus_language');

        // Experience
        $this->addSql('DROP SEQUENCE admissibility_bonus_experience_id_seq CASCADE');
        $this->addSql('DROP TABLE admissibility_bonus_experience');
    }
}
