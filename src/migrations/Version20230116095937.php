<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230116095937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add bloc NOTIFICATION_EXAM_SESSION_DELETE';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'NOTIFICATION_EXAM_SESSION_DELETE', NOW(), NOW())");

        $this->addSql("INSERT INTO bloc (
            id,
            tag_id,
            key,
            label,
            content,
            position,
            active,
            created_at,
            updated_at
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_EXAM_SESSION_DELETE'),
            'NOTIFICATION_EXAM_SESSION_DELETE',
            'Votre inscription à la session Skema est annulée',
            '<p>Bonjour %firstname%, Votre inscription à la session Skema pour l’épreuve %nom_typologie% du %date_start% a été annulée.</p> <p>Le service concours Skema.</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id)
            SELECT  (SELECT MAX(id) FROM bloc), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");
    }

    public function down(Schema $schema): void
    {
    }
}
