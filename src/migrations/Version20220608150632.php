<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220608150632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Fix bloc notification checkDiploma';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc SET key = 'NOTIFICATION_CHECK_DIPLOMA' WHERE id = 75;");
    }

    public function down(Schema $schema): void
    {
    }
}
