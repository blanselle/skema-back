<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114140843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New bloc for candidacy submission error';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ERROR_CANDIDACY_SUBMISSION_POPIN', NOW(), NOW())");

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
                (SELECT id FROM bloc_tag WHERE label = 'ERROR_CANDIDACY_SUBMISSION_POPIN'),
                'ERROR_CANDIDACY_SUBMISSION_POPIN',
                'ERROR_CANDIDACY_SUBMISSION_POPIN',
                '<p>La soumission de votre candidature n’a pas pu aboutir, merci de contacter le service concours.</p>',
                '0',
                '1',
                NOW(),
                NOW()
            )");
    }

    public function down(Schema $schema): void {}
}
