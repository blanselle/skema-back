<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220602142140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMA] Replace tag to tags in bac_type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM bac_type');
        $this->addSql('ALTER TABLE bac_type ADD tags TEXT NOT NULL');
        $this->addSql('ALTER TABLE bac_type DROP tag');
        $this->addSql('COMMENT ON COLUMN bac_type.tags IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM bac_type');
        $this->addSql('ALTER TABLE bac_type DROP tags');
        $this->addSql('ALTER TABLE bac_type ADD tag VARCHAR(50) NOT NULL');
    }
}
