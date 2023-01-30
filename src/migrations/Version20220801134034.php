<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220801134034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMAS] Notification sender id null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ALTER sender_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ALTER sender_id SET NOT NULL');
    }
}
