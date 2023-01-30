<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220620094138 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE experience ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE experience ALTER description DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE experience ALTER description TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE experience ALTER description DROP DEFAULT');
    }
}
