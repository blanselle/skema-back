<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221115162827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix bug dans les migrations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE student ADD COLUMN IF NOT EXISTS admissibility_max_score DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE student DROP admissibility_max_score');
    }
}
