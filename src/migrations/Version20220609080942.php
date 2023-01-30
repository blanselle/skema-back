<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220609080942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Fix media ids';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("SELECT setval('media_id_seq', 26, true)");
    }

    public function down(Schema $schema): void
    {
    }
}
