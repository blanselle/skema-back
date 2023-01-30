<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111210922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Oral Test] - Add unique index on campus_oral_test table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX oral_test_campus_oral_day_unique_index ON campus_oral_day (configuration_id, first_language_id, second_language_id, date)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX oral_test_campus_oral_day_unique_index');
    }
}
