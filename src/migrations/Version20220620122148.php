<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220620122148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMAS] schoolReport.score nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE school_report ALTER score DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE school_report ALTER score SET NOT NULL');
    }
}
