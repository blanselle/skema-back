<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220607123212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Add position on resignation blocs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE bloc SET position = 2 WHERE id = 70;');
        $this->addSql('UPDATE bloc SET position = 3 WHERE id = 71;');
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (62, 'RESIGNATION_MESSAGE_SUCCESS', now(), now());");
        $this->addSql('UPDATE bloc SET tag_id = 62 WHERE id = 72;');
    }

    public function down(Schema $schema): void
    {
    }
}
