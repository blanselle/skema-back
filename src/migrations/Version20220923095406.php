<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220923095406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_calculator DROP CONSTRAINT fk_b904d8f9a76ed395');
        $this->addSql('DROP INDEX idx_b904d8f9a76ed395');
        $this->addSql('ALTER TABLE admissibility_calculator ALTER last_launch_date DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_calculator ALTER last_launch_date SET NOT NULL');
        $this->addSql('ALTER TABLE admissibility_calculator ADD CONSTRAINT fk_b904d8f9a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b904d8f9a76ed395 ON admissibility_calculator (user_id)');
    }
}
