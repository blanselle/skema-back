<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220811103027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMAS] Add delete cascade program_channel bonus';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type DROP CONSTRAINT fk_fc0e4bf364cf5c1e');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type ADD CONSTRAINT FK_32CA6564CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_basic DROP CONSTRAINT fk_45acd1e664cf5c1e');
        $this->addSql('ALTER TABLE admissibility_bonus_basic ADD CONSTRAINT FK_F457629C64CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP CONSTRAINT fk_fc0e4bf364cf5c1f');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD CONSTRAINT FK_6377D14664CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_experience DROP CONSTRAINT fk_fcca036c64cf5c1e');
        $this->addSql('ALTER TABLE admissibility_bonus_experience ADD CONSTRAINT FK_9059146664CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_language DROP CONSTRAINT fk_e0c07b2b64cf5c1e');
        $this->addSql('ALTER TABLE admissibility_bonus_language ADD CONSTRAINT FK_977D29F564CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level DROP CONSTRAINT fk_96743e3564cf5c1e');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level ADD CONSTRAINT FK_13DBF10864CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type DROP CONSTRAINT FK_32CA6564CF5C1E');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type ADD CONSTRAINT fk_fc0e4bf364cf5c1e FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP CONSTRAINT FK_6377D14664CF5C1E');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD CONSTRAINT fk_fc0e4bf364cf5c1f FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level DROP CONSTRAINT FK_13DBF10864CF5C1E');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level ADD CONSTRAINT fk_96743e3564cf5c1e FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_basic DROP CONSTRAINT FK_F457629C64CF5C1E');
        $this->addSql('ALTER TABLE admissibility_bonus_basic ADD CONSTRAINT fk_45acd1e664cf5c1e FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_language DROP CONSTRAINT FK_977D29F564CF5C1E');
        $this->addSql('ALTER TABLE admissibility_bonus_language ADD CONSTRAINT fk_e0c07b2b64cf5c1e FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE admissibility_bonus_experience DROP CONSTRAINT FK_9059146664CF5C1E');
        $this->addSql('ALTER TABLE admissibility_bonus_experience ADD CONSTRAINT fk_fcca036c64cf5c1e FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
