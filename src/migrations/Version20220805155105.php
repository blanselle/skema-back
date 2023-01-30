<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220805155105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Admissibility param new field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_param ADD file VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_param DROP file');
    }
}
