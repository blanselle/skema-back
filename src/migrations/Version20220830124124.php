<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220830124124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New fixture bloc';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_MESSAGES'), NULL, 'RESIGNATION_NOTIFICATION', 'Votre candidature Skema a été annulée', 'Bonjour %firstName%, votre candidature pour le concours PGE Skema a été annulé pour le motif suivant : %motif%. Le Service Concours', NULL, NULL, '1', '1', now(), now())");
    }

    public function down(Schema $schema): void
    {
    }
}
