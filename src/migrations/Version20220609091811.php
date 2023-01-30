<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220609091811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATE] Delete sc user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM users WHERE id = '1ece2524-e10d-6be8-8bcd-556800179a58'");
    }

    public function down(Schema $schema): void
    {
    }
}
