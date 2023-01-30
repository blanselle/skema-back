<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220809070943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMA] Add note and bonus in CV';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cv ADD note DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE cv ADD bonus DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cv DROP note');
        $this->addSql('ALTER TABLE cv DROP bonus');
    }
}
