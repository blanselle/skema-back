<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220824183739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student ADD english_note_used_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD management_note_used_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF335EB853BA FOREIGN KEY (english_note_used_id) REFERENCES exam_student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF331BBE0569 FOREIGN KEY (management_note_used_id) REFERENCES exam_student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF335EB853BA ON student (english_note_used_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF331BBE0569 ON student (management_note_used_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF335EB853BA');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF331BBE0569');
        $this->addSql('DROP INDEX UNIQ_B723AF335EB853BA');
        $this->addSql('DROP INDEX UNIQ_B723AF331BBE0569');
        $this->addSql('ALTER TABLE student DROP english_note_used_id');
        $this->addSql('ALTER TABLE student DROP management_note_used_id');
    }
}
