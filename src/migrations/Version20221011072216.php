<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221011072216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New blocs fixtures';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'CV_INFO_MOYENNE', NOW(), NOW())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'CV_INFO_MOYENNE_NON_STANDARD', NOW(), NOW())");

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
            (SELECT id FROM bloc_tag WHERE label = 'CV_INFO_MOYENNE'),
            null,
            'Votre moyenne',
            '<p>Saisir votre moyenne générale si votre relevé de note présente une moyenne sur 20</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

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
            (SELECT id FROM bloc_tag WHERE label = 'CV_INFO_MOYENNE_NON_STANDARD'),
            null,
            'Pas de moyenne',
            '<p>Vous n’avez pas de moyenne générale sur 20, cocher cette case et pensez à joindre votre relevé de note ou votre justificatif</p>',
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
