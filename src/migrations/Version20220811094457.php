<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220811094457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Add key to ast1';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE program_channel SET key='ast1' WHERE name LIkE 'AST 1'");
        $this->addSql("UPDATE program_channel SET key='ast2' WHERE name LIkE 'AST 2'");
    }

    public function down(Schema $schema): void
    {
    }
}
