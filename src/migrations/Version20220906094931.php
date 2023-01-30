<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906094931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New fixture bloc';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_MESSAGES'), NULL, 'CONTACT_NOTIFICATION', 'Demande de contact', 'Merci ! Votre demande sera traitée dans les plus brefs délai.', NULL, NULL, '1', '1', now(), now())");
    }

    public function down(Schema $schema): void
    {
    }
}
