<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704151958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_diploma ADD dual_path_student_diploma_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student_diploma ADD CONSTRAINT FK_1173CAFBD5501BA FOREIGN KEY (dual_path_student_diploma_id) REFERENCES student_diploma (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1173CAFBD5501BA ON student_diploma (dual_path_student_diploma_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_diploma DROP CONSTRAINT FK_1173CAFBD5501BA');
        $this->addSql('DROP INDEX UNIQ_1173CAFBD5501BA');
        $this->addSql('ALTER TABLE student_diploma DROP dual_path_student_diploma_id');
    }
}
