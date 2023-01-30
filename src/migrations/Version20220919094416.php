<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220919094416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session_type ADD code VARCHAR(25) DEFAULT NULL');
        $this->addSql("UPDATE exam_session_type SET code='ANG' where exam_session_type.name = 'Anglais'");
        $this->addSql("UPDATE exam_session_type SET code='MANAGEMENT' where exam_session_type.name = 'Management'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session_type DROP code');
    }
}
