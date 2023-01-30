<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230124181419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Bloc update PROGRAM_CHANNEL_SWITCHED';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'PROGRAM_CHANNEL_SWITCHED', NOW(), NOW())");

        $this->addSql("UPDATE bloc set content='<p>Votre candidature a été modifiée. Vous candidatez désormais dans la voie de concours %default.libelle_voie%</p>', tag_id=(select id from bloc_tag where label='PROGRAM_CHANNEL_SWITCHED') where key='PROGRAM_CHANNEL_SWITCHED'");

        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id)
            SELECT  (SELECT id FROM bloc where key='PROGRAM_CHANNEL_SWITCHED'), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");
    }

    public function down(Schema $schema): void
    {}
}
