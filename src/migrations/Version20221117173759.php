<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221117173759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'NOTIFICATION_EXAM_SESSION_RESIGNATION', NOW(), NOW())");

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
            (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_EXAM_SESSION_RESIGNATION'),
            'NOTIFICATION_EXAM_SESSION_RESIGNATION',
            'Annulation de votre score %nom_typologie%',
            '<p>Bonjour %firstname%, votre saisie pour l’épreuve écrite %typologie% a été annulée.</p> <p>Le service concours.</p>',
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
