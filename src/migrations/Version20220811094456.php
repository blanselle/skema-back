<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220811094456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMA] Add key to program_channel';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program_channel ADD key VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_291376938A90ABA9 ON program_channel (key)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_291376938A90ABA9');
        $this->addSql('ALTER TABLE program_channel DROP key');
    }
}
