<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221114094139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Add bloc administrativeRecord completion';
    }

    public function up(Schema $schema): void
    {
        $this->addBloc(
            'ADMINISTRATIVE_RECORD_ERROR_MESSAGE_CHECK_DIPLOMA',
            "Le service concours doit valider l''éligibilité de votre diplôme pour que vous puissiez continuer votre candidature",
        );
        $this->addBloc(
            'ADMINISTRATIVE_RECORD_ERROR_MESSAGE',
            'Vous ne pouvez pas valider votre dossier administratif. Contactez le service concours.',
        ); 
    }

    public function addBloc(string $key, string $content): void
    {
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
            (SELECT id FROM bloc_tag WHERE label = 'ERRORS'),
            '$key',
            null,
            '$content',
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
