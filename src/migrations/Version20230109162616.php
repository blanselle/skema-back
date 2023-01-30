<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109162616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addBloc(
            key: 'PROGRAM_CHANNEL_SWITCHED',
            label: 'Modification de votre voie de concours',
            content: "<p>Votre candidature a été modifiée. Vous candidater désormais dans la voie de concours %default.libelle_voie%</p>",
        );
    }

    public function down(Schema $schema): void
    {

    }

    private function addBloc(string $key, string $label = '', string $content = ''): void
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
            (SELECT id FROM bloc_tag WHERE label = 'ACCOUNT_ACTIVATION'),
            :key,
            :label,
            :content,
            '0',
            '1',
            NOW(),
            NOW()
        )", [
            'key' => $key,
            'label' => $label,
            'content' => $content,
        ]);
    }
}
