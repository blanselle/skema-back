<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Constants\CV\DistinctionCodeConstants;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220829134331 extends AbstractMigration
{
    private array $programChannels = ['AST 1', 'AST 2', 'BCE Économique', 'BCE Littéraire'];

    public function getDescription(): string
    {
        return 'Bonus distinction string to object';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM admissibility_bonus_distinction');

        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD bac_distinction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP distinction');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD CONSTRAINT FK_6377D14633974808 FOREIGN KEY (bac_distinction_id) REFERENCES bac_distinction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6377D14633974808 ON admissibility_bonus_distinction (bac_distinction_id)');

        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_TRES_BIEN,  '1.00');
        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_BIEN,       '0.50');
        $this->addBacDistinctionBonus(DistinctionCodeConstants::DISTINCTION_ASSEZ_BIEN, '0.25');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP CONSTRAINT FK_6377D14633974808');
        $this->addSql('DROP INDEX IDX_6377D14633974808');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction ADD distinction VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_bonus_distinction DROP bac_distinction_id');
    }

    private function addBacDistinctionBonus(string $distinction, string $value): void
    {
        $category = "SELECT id FROM admissibility_bonus_category WHERE key LIKE '" . BonusNameConstants::BAC_DISTINCTION . "'";
        $distinction = "SELECT id FROM bac_distinction WHERE code LIKE '" . $distinction . "'";

        foreach($this->programChannels as $programChannel) {

            $sqlProgramChannel = "SELECT id FROM program_channel WHERE name LIKE '{$programChannel}'";

            $this->addSql("INSERT INTO admissibility_bonus_distinction (id, bac_distinction_id, program_channel_id, category_id, value)
            VALUES (nextval('admissibility_bonus_distinction_id_seq'), ({$distinction}), ({$sqlProgramChannel}), ({$category}), ({$value}));");
        }
    }
}
