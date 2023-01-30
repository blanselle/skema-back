<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220921111613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE loggable_history DROP CONSTRAINT FK_AE4E0B5FCB944F1A');
        $this->addSql('ALTER TABLE loggable_history ADD CONSTRAINT FK_AE4E0B5FCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE loggable_history DROP CONSTRAINT fk_ae4e0b5fcb944f1a');
        $this->addSql('ALTER TABLE loggable_history ADD CONSTRAINT fk_ae4e0b5fcb944f1a FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
