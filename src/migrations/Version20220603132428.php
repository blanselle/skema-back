<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220603132428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New field experience entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE experience ADD state VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE experience DROP state');
    }
}
