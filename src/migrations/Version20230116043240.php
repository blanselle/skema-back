<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230116043240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Oral Test] - Add block';
    }

    public function up(Schema $schema): void
    {
        $tags = [
            ['label' => 'ORAL_TEST'],
        ];
        foreach ($tags as $tag) {
            $this->addTag($tag['label']);
        }

        $blocs = [
            ['tag' => 'ORAL_TEST', 'key' => 'ORAL_TEST_NO_SLOT_MESSAGE', 'label' => NULL, 'content' => '<p>Il n’y a plus de créneaux disponibles pour vos épreuves. Merci de contacter le service concours</p>', 'position' => 0],
            ['tag' => 'ORAL_TEST', 'key' => 'ORAL_TEST_REJECTED_MESSAGE', 'label' => NULL, 'content' => '<p>Ce créneau vient d’être réservé, merci d’en sélectionner un nouveau</p>', 'position' => 0],
            ['tag' => 'ORAL_TEST', 'key' => 'ORAL_TEST_WAITING_MESSAGE', 'label' => NULL, 'content' => '<p>Merci de patienter, nous vérifions la disponibilité de votre créneau</p>', 'position' => 0],
        ];
        foreach ($blocs as $bloc) {
            $this->addBloc($bloc);
        }
    }

    public function down(Schema $schema): void
    {}

    private function addBloc(array $bloc): void
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
            (SELECT id FROM bloc_tag WHERE label = :tag),
            :key,
            :label,
            :content,
            :position,
            '1',
            NOW(),
            NOW()
        )", [
            'tag' => $bloc['tag'],
            'key' => $bloc['key'],
            'label' => $bloc['label'],
            'content' => $bloc['content'],
            'position' => $bloc['position'],
        ]);


        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id)
            SELECT  (SELECT MAX(id) FROM bloc), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");
    }

    private function addTag(string $label): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), '$label', NOW(), NOW())");
    }
}
