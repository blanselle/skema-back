<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220727133931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add valdated field in cv';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cv ADD validated BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cv DROP validated');
    }
}
