<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221028140623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Create bloc admissibility result';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'ADMISSIBILITY_RESULT', NOW(), NOW())");

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
            (SELECT id FROM bloc_tag WHERE label = 'ADMISSIBILITY_RESULT'),
            'ADMISSIBILITY_RESULT_VERBATIM',
            'Votre moyenne',
            '<p>Retrouver ci-dessous votre score de dossier d’admissibilité. <br /> A la suite des épreuves orales, vos résultats d’admission seront disponibles à partir de %parameter.dateAffectationDefinitive%</p>',
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
