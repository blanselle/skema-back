<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221109140056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New Bloc for History orders page and add admissibility max score in student';
    }


    public function up(Schema $schema): void
    {
        $blocs = [
            ['tag' => 'HISTORY_ORDERS_PAGE_VERBATIM', 'content' => '<p>Retrouvez ici vos paiements effectués</p>'],
        ];

        foreach ($blocs as $bloc) {
            $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), '{$bloc['tag']}', NOW(), NOW())");

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
                (SELECT id FROM bloc_tag WHERE label = '{$bloc['tag']}'),
                null,
                null,
                '{$bloc['content']}',
                '0',
                '1',
                NOW(),
                NOW()
            )");
        }

        $stmt = $this->connection->executeQuery("SELECT id FROM program_channel");
        $programChannels = $stmt->fetchAllAssociative();
        $stmt = $this->connection->executeQuery("SELECT id FROM bloc LEFT JOIN bloc_program_channel bpc on bloc.id = bpc.bloc_id WHERE bpc.bloc_id IS NULL ORDER BY id ASC");
        $blocs = $stmt->fetchAllAssociative();

        foreach ($blocs as $entity) {
            foreach($programChannels as $programChannel) {
                $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ({$entity['id']}, {$programChannel['id']})");
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}
