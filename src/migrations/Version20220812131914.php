<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Admissibility\Bonus\BonusListConstants;
use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Constants\CV\DistinctionCodeConstants;
use App\Constants\CV\DistinctionConstants;
use App\Constants\CV\Experience\ExperienceTypeConstants;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220812131914 extends AbstractMigration
{
    private array $programChannels = ['AST 1', 'AST 2', 'BCE Économique', 'BCE Littéraire'];
                    
    public function getDescription(): string
    {
        return '[DATA] Insert bonuses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM admissibility_bonus_bac_type");
        $this->addSql("DELETE FROM admissibility_bonus_basic");
        $this->addSql("DELETE FROM admissibility_bonus_distinction");
        $this->addSql("DELETE FROM admissibility_bonus_experience");
        $this->addSql("DELETE FROM admissibility_bonus_language");
        $this->addSql("DELETE FROM admissibility_bonus_sport_level");

        $this->addSql('CREATE SEQUENCE admissibility_bonus_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_bonus_category (id INT NOT NULL, name VARCHAR(180) NOT NULL, key VARCHAR(180) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type DROP name');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type ADD CONSTRAINT FK_32CA6512469DE2 FOREIGN KEY (category_id) REFERENCES admissibility_bonus_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_32CA6512469DE2 ON admissibility_bonus_bac_type (category_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_basic ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_basic DROP name');
        $this->addSql('ALTER TABLE admissibility_bonus_basic ADD CONSTRAINT FK_F457629C12469DE2 FOREIGN KEY (category_id) REFERENCES admissibility_bonus_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F457629C12469DE2 ON admissibility_bonus_basic (category_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP name');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD CONSTRAINT FK_6377D14612469DE2 FOREIGN KEY (category_id) REFERENCES admissibility_bonus_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6377D14612469DE2 ON admissibility_bonus_distinction (category_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_experience ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_experience DROP name');
        $this->addSql('ALTER TABLE admissibility_bonus_experience ADD CONSTRAINT FK_9059146612469DE2 FOREIGN KEY (category_id) REFERENCES admissibility_bonus_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9059146612469DE2 ON admissibility_bonus_experience (category_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_language ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_language DROP name');
        $this->addSql('ALTER TABLE admissibility_bonus_language ADD CONSTRAINT FK_977D29F512469DE2 FOREIGN KEY (category_id) REFERENCES admissibility_bonus_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_977D29F512469DE2 ON admissibility_bonus_language (category_id)');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level DROP name');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level ADD CONSTRAINT FK_13DBF10812469DE2 FOREIGN KEY (category_id) REFERENCES admissibility_bonus_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_13DBF10812469DE2 ON admissibility_bonus_sport_level (category_id)');

        $this->addCategory('Type de bac', BonusNameConstants::BAC_TYPE);
        $this->addCategory('Double diplôme', BonusNameConstants::ADDITIONNAL);
        $this->addCategory('Mention', BonusNameConstants::BAC_DISTINCTION);
        $this->addCategory('Niveau sportif', BonusNameConstants::SPORT_LEVEL);
        $this->addCategory('Langues', BonusNameConstants::LANGUAGE);
        $this->addCategory('Experience', BonusNameConstants::EXPERIENCE);

        $this->addBacTypeBonus('Littéraire',           '0.25');
        $this->addBacTypeBonus('Scientifique',         '0.50');
        $this->addBacTypeBonus('Économique et social', '0.00');

        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_TRES_BIEN,  '1.00');
        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_BIEN,       '0.50');
        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_ASSEZ_BIEN, '0.25');

        unset($this->programChannels['BCE Économique']);
        unset($this->programChannels['BCE Littéraire']);

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


        $this->addSportLevelBonus('Élite / Sénior / Reconversion',                      '3');
        $this->addSportLevelBonus('Relève / Jeune',                                     '2');
        $this->addSportLevelBonus('Espoir / Collectifs nationaux et partenaires',       '1');
        $this->addSportLevelBonus('Professionnel / Arbitre Entraineur Régional',        '1');
        $this->addSportLevelBonus('Professionnel / Arbitre Entraineur National',        '2');
        $this->addSportLevelBonus('Professionnel / Arbitre Entraineur International',   '3');
    }

    private function addCategory(string $name, string $key): void
    {
        $this->addSql("INSERT INTO admissibility_bonus_category (id, name, key)
        VALUES (nextval('admissibility_bonus_category_id_seq'), '{$name}', '{$key}');");
    }

    private function addBacTypeBonus(string $bacType, string $value): void 
    {
        $sqlBacType = "SELECT id FROM bac_type WHERE name LIKE '{$bacType}'";
        $category = "SELECT id FROM admissibility_bonus_category WHERE key LIKE '" . BonusNameConstants::BAC_TYPE . "'";
        
        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_bac_type (id, bac_type_id, program_channel_id, category_id, value)
            VALUES (nextval('admissibility_bonus_bac_type_id_seq'), ({$sqlBacType}), ({$sqlProgramChannel}), ({$category}), ({$value}));");
        }
    }

    private function addBacDistinctionBonus(string $distinction, string $value): void 
    {
        $category = "SELECT id FROM admissibility_bonus_category WHERE key LIKE '" . BonusNameConstants::BAC_DISTINCTION . "'";

        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_distinction (id, distinction, program_channel_id, category_id, value)
            VALUES (nextval('admissibility_bonus_distinction_id_seq'), '{$distinction}', ({$sqlProgramChannel}), ({$category}), ({$value}));");
        }
    }

    private function addSportLevelBonus(string $sportLevel, string $value): void
    {
        $sqlSportLevel = "SELECT id FROM sport_level WHERE label LIKE '{$sportLevel}'";
        $category = "SELECT id FROM admissibility_bonus_category WHERE key LIKE '" . BonusNameConstants::SPORT_LEVEL . "'";

        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_sport_level (id, sport_level_id, program_channel_id, category_id, value)
            VALUES (nextval('admissibility_bonus_sport_level_id_seq'), ({$sqlSportLevel}), ({$sqlProgramChannel}), ({$category}), ({$value}));");
        }
    }

    private function addBasicBonus(string $name, string $value): void
    {
        $category = "SELECT id FROM admissibility_bonus_category WHERE key LIKE '$name'";

        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_basic (id, program_channel_id, category_id, value)
            VALUES (nextval('admissibility_bonus_basic_id_seq'), ({$sqlProgramChannel}), ({$category}), ({$value}));");
        }
    }

    private function addLanguageBonus(string $min, string $value): void
    {
        $category = "SELECT id FROM admissibility_bonus_category WHERE key LIKE '" . BonusNameConstants::LANGUAGE . "'";

        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_language (id, program_channel_id, category_id, value, min)
            VALUES (nextval('admissibility_bonus_language_id_seq'), ({$sqlProgramChannel}), ({$category}), ({$value}), $min);");
        }
    }

    private function addExperience(array $types, int $duration, string $programChannel, string $value): void
    {
        $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";
        $category = "SELECT id FROM admissibility_bonus_category WHERE key LIKE '" . BonusNameConstants::EXPERIENCE . "'";

        foreach($types as $type) {
            $this->addSql("INSERT INTO admissibility_bonus_experience (id, program_channel_id, category_id, value, type, duration)
            VALUES (nextval('admissibility_bonus_experience_id_seq'), ({$sqlProgramChannel}), ({$category}), ({$value}), '{$type}', {$duration});");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type DROP CONSTRAINT FK_32CA6512469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_basic DROP CONSTRAINT FK_F457629C12469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP CONSTRAINT FK_6377D14612469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_experience DROP CONSTRAINT FK_9059146612469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_language DROP CONSTRAINT FK_977D29F512469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level DROP CONSTRAINT FK_13DBF10812469DE2');
        $this->addSql('DROP SEQUENCE admissibility_bonus_category_id_seq CASCADE');
        $this->addSql('DROP TABLE admissibility_bonus_category');
        $this->addSql('DROP INDEX IDX_32CA6512469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type ADD name VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_bac_type DROP category_id');
        $this->addSql('DROP INDEX IDX_6377D14612469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD name VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP category_id');
        $this->addSql('DROP INDEX IDX_13DBF10812469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level ADD name VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_sport_level DROP category_id');
        $this->addSql('DROP INDEX IDX_F457629C12469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_basic ADD name VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_basic DROP category_id');
        $this->addSql('DROP INDEX IDX_977D29F512469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_language ADD name VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_language DROP category_id');
        $this->addSql('DROP INDEX IDX_9059146612469DE2');
        $this->addSql('ALTER TABLE admissibility_bonus_experience ADD name VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_experience DROP category_id');
    }
}
