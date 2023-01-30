<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220818122511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add field intern in program_channel';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program_channel ADD intern BOOLEAN');

        $this->addSql('UPDATE program_channel SET intern = false');
        $this->addSql("UPDATE program_channel SET intern = true WHERE name LIKE 'AST%'");
        $this->addSql('ALTER TABLE program_channel ALTER intern SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program_channel DROP intern');
    }
}
