<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220628090109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move type field from school_reports to bac_sups';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac_sup ADD type VARCHAR(50) NOT NULL DEFAULT \'annuel\'');
        $this->addSql('ALTER TABLE exam_session ALTER distributed DROP DEFAULT');
        $this->addSql('ALTER TABLE school_report DROP type');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac_sup DROP type');
        $this->addSql('ALTER TABLE school_report ADD type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE exam_session ALTER distributed SET DEFAULT false');
    }
}
