<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220817150530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'INE can be null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac ALTER ine DROP DEFAULT');
        $this->addSql('ALTER TABLE bac ALTER ine DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac ALTER ine SET DEFAULT \'\'');
        $this->addSql('ALTER TABLE bac ALTER ine SET NOT NULL');
    }
}
