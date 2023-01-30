<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221128095325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMA] examStudent score to float';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_student ALTER score TYPE DOUBLE PRECISION');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_student ALTER score TYPE INT');
    }
}
