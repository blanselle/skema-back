<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220609081722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] fix media fixture cms';
    }

    public function up(Schema $schema): void
    {
        for($i = 1; $i <= 23; $i++) {
            $this->updateFixtureFile();
        }
    }

    public function down(Schema $schema): void
    {
    }

    private function updateFixtureFile(): void
    {
        $this->addSql("UPDATE media SET file = CONCAT('public/', 
            (SELECT file FROM media WHERE file NOT LIKE 'public%' LIMIT 1)
        ) WHERE id = (
            SELECT id FROM media WHERE file NOT LIKE 'public%' LIMIT 1
        );
        ");
    }
}




