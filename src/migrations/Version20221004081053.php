<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221004081053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Blocs dashboard insertions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO media (id, file, original_name, created_at, updated_at, state, transition, type, code, student_id) VALUES (nextval('media_id_seq'),'fixtures/skema/skema_applaudissement-min.png','skema_applaudissement-min.png',now(),now(),'uploaded','','image_cms','autre',NULL)");
        $this->addSql("INSERT INTO media (id, file, original_name, created_at, updated_at, state, transition, type, code, student_id) VALUES (nextval('media_id_seq'),'fixtures/skema/skema_festif-min.png','skema_festif-min.png',now(),now(),'uploaded','','image_cms','autre',NULL)");

        $this->addSql("SELECT setval('bloc_tag_id_seq', (SELECT MAX(id) FROM bloc_tag), true)");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_INITIALIZED_CANDIDACY', NOW(), NOW())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_COMPLETED_CANDIDACY', NOW(), NOW())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_SAVED_CANDIDACY', NOW(), NOW())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_VALIDATED_CANDIDACY', NOW(), NOW())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_ADMISSIBLE_CANDIDACY', NOW(), NOW())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_CAMPUS_INFORMATIONS', NOW(), NOW())");

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
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_INITIALIZED_CANDIDACY'),
            '',
            null,
            '<p><strong>Complétez votre dossier administratif</strong> pour confirmer votre éligibilité au concours.</p>',
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
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_COMPLETED_CANDIDACY'),
            null,
            null,
            '<p><strong>Votre candidature est complète</strong></p>',
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
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_SAVED_CANDIDACY'),
            null,
            null,
            '<p><strong>Votre candidature est bien enregistrée.</strong><br />Elle est actuellement en cours de validation par nos services.</p>',
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
            updated_at,
            media_id
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_VALIDATED_CANDIDACY'),
            null,
            'Votre candidature est validée',
            '<p>Rendez-vous <strong>le %date_results%</strong> pour consulter les résultats d’admissibilité !</p>',
            '0',
            '1',
            NOW(),
            NOW(),
            (SELECT id FROM media WHERE original_name = 'skema_applaudissement-min.png')
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
            updated_at,
            media_id
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_ADMISSIBLE_CANDIDACY'),
            null,
            'Bravo !',
            '<p>Vous êtes admissible aux épreuves orales du Concours.<br />Vous avez <strong>jusqu’au %date_admission_epreuves% pour choisir une date et un campus</strong> pour passer vos épreuves orales (en fonction des places disponibles)</p>',
            '0',
            '1',
            NOW(),
            NOW(),
            (SELECT id FROM media WHERE original_name = 'skema_festif-min.png')
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
            updated_at,
            link,
            label_link
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_CAMPUS_INFORMATIONS'),
            null,
            null,
            '<p><strong>Envie d’en savoir plus sur les campus ?</strong></p>',
            '0',
            '1',
            NOW(),
            NOW(),
            'https://www.skema-bs.fr/skema/campus',
            'Découvrir les campus'
        )");
    }

    public function down(Schema $schema): void
    {
    }
}
