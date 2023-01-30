<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220630120637 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session ADD type VARCHAR(15) NOT NULL DEFAULT \'Skema\'');
        $this->addSql('ALTER TABLE exam_session ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_session ADD fneige VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session DROP type');
        $this->addSql('ALTER TABLE exam_session DROP city');
        $this->addSql('ALTER TABLE exam_session DROP fneige');
    }
}
