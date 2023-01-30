<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221117160551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'need_confirmation field removed';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_classification DROP need_confirmation');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_classification ADD need_confirmation BOOLEAN NOT NULL');
    }
}
