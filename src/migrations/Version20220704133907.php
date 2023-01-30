<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704133907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bac_sup ADD dual_path_bac_sup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bac_sup ADD CONSTRAINT FK_5BEB66A2733C00C2 FOREIGN KEY (dual_path_bac_sup_id) REFERENCES bac_sup (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5BEB66A2733C00C2 ON bac_sup (dual_path_bac_sup_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bac_sup DROP CONSTRAINT FK_5BEB66A2733C00C2');
        $this->addSql('DROP INDEX UNIQ_5BEB66A2733C00C2');
        $this->addSql('ALTER TABLE bac_sup DROP dual_path_bac_sup_id');
    }
}
