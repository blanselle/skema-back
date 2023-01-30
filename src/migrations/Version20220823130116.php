<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220823130116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'School Report new field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE school_report ADD score_not_out_of_twenty BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE school_report DROP score_not_out_of_twenty');
    }
}
