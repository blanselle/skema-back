<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Constants\CV\DistinctionCodeConstants;
use App\Constants\CV\DistinctionConstants;
use App\Constants\CV\Experience\ExperienceTypeConstants;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220831114344 extends AbstractMigration
{

    public function getDescription(): string
    {
        return '[DATA] update Associative Experience bonuses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('delete from admissibility_bonus_experience');

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


        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 0, 'AST 1', '0.25');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 0, 'AST 2', '0.2');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 2, 'AST 1', '0.5');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 2, 'AST 2', '0.4');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 3, 'AST 1', '0.75');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 3, 'AST 2', '0.6');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 4, 'AST 1', '1');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 4, 'AST 2', '0.8');
        $this->addExperience([ExperienceTypeConstants::TYPE_ASSOCIATIVE], 5, 'AST 2', '1');
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
    }
}
