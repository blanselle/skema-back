<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220623083257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New field distribution in exam_session';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session ADD distributed BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session DROP distributed');
    }
}
