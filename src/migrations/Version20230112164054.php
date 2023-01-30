<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230112164054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX oral_test_oral_test_student_unique_index');
        $this->addSql('CREATE UNIQUE INDEX oral_test_oral_test_student_unique_index ON oral_test_oral_test_student (student_id) WHERE (state <> \'rejected\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX oral_test_oral_test_student_unique_index');
        $this->addSql('CREATE UNIQUE INDEX oral_test_oral_test_student_unique_index ON oral_test_oral_test_student (campus_oral_day_id, student_id) WHERE ((state)::text <> \'rejected\'::text)');
    }
}
