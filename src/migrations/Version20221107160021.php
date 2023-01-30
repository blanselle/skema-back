<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221107160021 extends AbstractMigration
{
    private const EXAM_CLASSIFICATION = [
        'iCIMS®' => 'icims',
        'Tage 2®' => 'tage2',
        'TOEIC®' => 'toiec',
        'TOEFL® ITP' => 'toefltip',
        'TOEFL® IBT' => 'toeflibt',
        'TAGE MAGE®' => 'tagemage',
        'IETLS®' => 'ietls',
        'GMAT®' => 'gmat',
    ];
    
    public function getDescription(): string
    {
        return '[DATA] Add key on exam_classification';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_classification ADD key VARCHAR(180) DEFAULT NULL');

        foreach(self::EXAM_CLASSIFICATION as $name => $key) {
            $this->addSql(sprintf("UPDATE exam_classification SET key='%s' WHERE name LIKE '%s'", $key, $name));
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_classification DROP key');
    }
}
