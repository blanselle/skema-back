<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221102155341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New bloc';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'DASHBOARD_COMPLETE_POPIN', NOW(), NOW())");

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
                (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_COMPLETE_POPIN'),
                null,
                null,
                '<p>Confirmez-vous la soumission de votre candidature ?</p>',
                '0',
                '1',
                NOW(),
                NOW()
            )");
    }

    public function down(Schema $schema): void
    {
    }
}
