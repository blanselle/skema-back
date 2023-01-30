<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220909134554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add position in program_channel';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program_channel ADD position INT DEFAULT NULL');
        $positions = [
            'AST 1' => 1,
            'AST 2' => 2,
            'BCE Économique' => 3,
            'BCE Littéraire' => 4,
        ];

        foreach($positions as $programChannel => $position) {
            $this->addSql('UPDATE program_channel SET position = :position WHERE name = :program_channel', [
                'position' => $position,
                'program_channel' => $programChannel,
            ]);
        }

        $this->addSql('ALTER TABLE program_channel ALTER "position" SET NOT NULL');
    }
    
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program_channel DROP position');
    }
}
