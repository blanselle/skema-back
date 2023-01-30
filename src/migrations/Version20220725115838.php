<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220725115838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix path medias';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE media SET file = 'public/fixtures/blocs/icon_info.svg' WHERE original_name = 'icon_info1.svg'");
        $this->addSql("UPDATE media SET file = 'public/fixtures/blocs/icon_info.svg' WHERE original_name = 'icon_info2.svg'");
        $this->addSql("UPDATE media SET file = 'public/fixtures/blocs/icon_info.svg' WHERE original_name = 'icon_info3.svg'");
        $this->addSql("UPDATE media SET file = 'public/fixtures/blocs/icon_voix.svg' WHERE original_name = 'icon_voix.svg'");
    }

    public function down(Schema $schema): void
    {
    }
}
