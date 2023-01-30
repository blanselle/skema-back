<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Constants\CV\DistinctionCodeConstants;
use App\Constants\CV\DistinctionConstants;
use App\Constants\CV\Experience\ExperienceTypeConstants;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220803090644 extends AbstractMigration
{
    private array $programChannels = ['AST 1', 'AST 2', 'BCE Économique', 'BCE Littéraire'];
                    
    public function getDescription(): string
    {
        return '[DATA] Insert bonuses';
    }

    public function up(Schema $schema): void
    {
        $this->addBacTypeBonus('Littéraire',           '0.25');
        $this->addBacTypeBonus('Scientifique',         '0.50');
        $this->addBacTypeBonus('Économique et social', '0.00');

        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_TRES_BIEN,  '1.00');
        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_BIEN,       '0.50');
        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_ASSEZ_BIEN, '0.25');

        unset($this->programChannels['BCE Économique']);
        unset($this->programChannels['BCE Littéraire']);

        // Add missing sport_level rows
        $this->addSql("INSERT INTO sport_level (id, label, position, created_at, updated_at)
        VALUES (nextval('sport_level_id_seq'), 'Professionnel / Arbitre Entraineur Régional', 4, '2022-05-31 17:28:04', '2022-05-31 17:28:04');");
        $this->addSql("INSERT INTO sport_level (id, label, position, created_at, updated_at)
        VALUES (nextval('sport_level_id_seq'), 'Professionnel / Arbitre Entraineur National', 5, '2022-05-31 17:28:04', '2022-05-31 17:28:04');");
        $this->addSql("INSERT INTO sport_level (id, label, position, created_at, updated_at)
        VALUES (nextval('sport_level_id_seq'), 'Professionnel / Arbitre Entraineur International', 6, '2022-05-31 17:28:04', '2022-05-31 17:28:04');");

        $this->addSportLevelBonus('Élite / Sénior / Reconversion',                      '3');
        $this->addSportLevelBonus('Relève / Jeune',                                     '2');
        $this->addSportLevelBonus('Espoir / Collectifs nationaux et partenaires',       '1');
        $this->addSportLevelBonus('Professionnel / Arbitre Entraineur Régional',        '1');
        $this->addSportLevelBonus('Professionnel / Arbitre Entraineur National',        '2');
        $this->addSportLevelBonus('Professionnel / Arbitre Entraineur International',   '3');

        $this->addBasicBonus(BonusNameConstants::ADDITIONNAL, '0.50');
        $this->addLanguageBonus('1', '0.25');
        $this->addLanguageBonus('3', '2.00');

        $proOrInterType = [ExperienceTypeConstants::TYPE_PROFESSIONAL, ExperienceTypeConstants::TYPE_INTERNATIONAL];
        $this->addExperience($proOrInterType, 0, 'AST 1', '0.25');
        $this->addExperience($proOrInterType, 0, 'AST 2', '0.2');
        $this->addExperience($proOrInterType, 2, 'AST 1', '0.5');
        $this->addExperience($proOrInterType, 2, 'AST 2', '0.4');
        $this->addExperience($proOrInterType, 3, 'AST 1', '0.75');
        $this->addExperience($proOrInterType, 3, 'AST 2', '0.6');
        $this->addExperience($proOrInterType, 4, 'AST 1', '0.1');
        $this->addExperience($proOrInterType, 4, 'AST 2', '0.8');
        $this->addExperience($proOrInterType, 5, 'AST 2', '1');


        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 0*12, 'AST 1', '0.25');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 0*12, 'AST 2', '0.2');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 2*12, 'AST 1', '0.5');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 2*12, 'AST 2', '0.4');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 3*12, 'AST 1', '0.75');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 3*12, 'AST 2', '0.6');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 4*12, 'AST 1', '1');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 4*12, 'AST 2', '0.8');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 5*12, 'AST 2', '1');
    }

    private function addBacTypeBonus(string $bacType, string $value): void 
    {
        $sqlBacType = "SELECT id FROM bac_type WHERE name LIKE '{$bacType}'";
        
        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_bac_type (id, bac_type_id, program_channel_id, name, value)
            VALUES (nextval('admissibility_bonus_bac_type_id_seq'), ({$sqlBacType}), ({$sqlProgramChannel}), '".BonusNameConstants::BAC_TYPE."', ({$value}));");
        }
    }

    private function addBacDistinctionBonus(string $distinction, string $value): void 
    {        
        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_distinction (id, distinction, program_channel_id, name, value)
            VALUES (nextval('admissibility_bonus_distinction_id_seq'), '{$distinction}', ({$sqlProgramChannel}), '".BonusNameConstants::BAC_DISTINCTION."', ({$value}));");
        }
    }

    private function addSportLevelBonus(string $sportLevel, string $value): void
    {
        $sqlSportLevel = "SELECT id FROM sport_level WHERE label LIKE '{$sportLevel}'";

        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_sport_level (id, sport_level_id, program_channel_id, name, value)
            VALUES (nextval('admissibility_bonus_sport_level_id_seq'), ({$sqlSportLevel}), ({$sqlProgramChannel}), '".BonusNameConstants::SPORT_LEVEL."', ({$value}));");
        }
    }

    private function addBasicBonus(string $name, string $value): void
    {
        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_basic (id, program_channel_id, name, value)
            VALUES (nextval('admissibility_bonus_basic_id_seq'), ({$sqlProgramChannel}), '{$name}', ({$value}));");
        }
    }

    private function addLanguageBonus(string $min, string $value): void
    {
        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_language (id, program_channel_id, name, value, min)
            VALUES (nextval('admissibility_bonus_language_id_seq'), ({$sqlProgramChannel}), '".BonusNameConstants::LANGUAGE."', ({$value}), $min);");
        }
    }

    private function addExperience(array $types, int $duration, string $programChannel, string $value): void
    {
        $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

        foreach($types as $type) {
            $this->addSql("INSERT INTO admissibility_bonus_experience (id, program_channel_id, name, value, type, duration)
            VALUES (nextval('admissibility_bonus_experience_id_seq'), ({$sqlProgramChannel}), '".BonusNameConstants::EXPERIENCE."', ({$value}), '{$type}', {$duration});");
        }
    }

    public function down(Schema $schema): void
    {
    }
}
