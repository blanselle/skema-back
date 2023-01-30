<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220922130445 extends AbstractMigration
{
    private const BAC_CHANNELS = ['Technologique' => 'technologique', 'Professionnel' => 'professional', 'Général' => 'general'];

    public function getDescription(): string
    {
        return '[DATA] Add key on bac_channel';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac_channel ADD key VARCHAR(180) DEFAULT NULL');

        foreach(self::BAC_CHANNELS as $name => $key) {
            $this->addSql(sprintf("UPDATE bac_channel SET key='%s' WHERE name LIKE '%s'", $key, $name));
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac_channel DROP key');
    }
}
