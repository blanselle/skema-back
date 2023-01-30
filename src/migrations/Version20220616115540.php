<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220616115540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMAS] Add ine on bac';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE bac ADD ine VARCHAR(50) NOT NULL DEFAULT ''");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac DROP ine');
    }
}
