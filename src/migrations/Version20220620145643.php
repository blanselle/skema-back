<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220620145643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Notification template new field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification_template ADD tag VARCHAR(20) DEFAULT \'\' NOT NULL');
        $this->addSql("UPDATE notification_template set tag = 'media_rejection'");
        $this->addSql("INSERT INTO notification_template (id, subject, content, tag) VALUES (nextval('notification_template_id_seq'), 'Autre',	'', 'media_transfer'), (nextval('notification_template_id_seq'), 'Document international',	'Le document n’est pas en français', 'media_transfer')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification_template DROP tag');
    }
}
