<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220616065102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert new object in notification_template';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("SELECT setval('notification_template_id_seq', 5, true)");
        $this->addSql("INSERT INTO notification_template (id, subject, content) VALUES (nextval('notification_template_id_seq'), 'Autre',	'')");
    }

    public function down(Schema $schema): void
    {
    }
}
