<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221006074222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Blocs dashboard insertions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO media (id, file, original_name, created_at, updated_at, state, transition, type, code, student_id) VALUES (nextval('media_id_seq'),'fixtures/skema/skema_sympa-min.png','skema_sympa-min.png',now(),now(),'uploaded','','image_cms','autre',NULL)");

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_PROCESSING_CANDIDACY', NOW(), NOW())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'DASHBOARD_WELCOME_CANDIDACY', NOW(), NOW())");

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
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_PROCESSING_CANDIDACY'),
            null,
            null,
            '<p><strong>Complétez et validez votre candidature</strong> afin que celle-ci puisse être vétifiée par nos services.</p>',
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
            (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_WELCOME_CANDIDACY'),
            null,
            null,
            '<p>Bienvenue dans votre espace candidat.<br />Vous pouvez maintenant compléter toutes les rubriques de votre candidature.</p>',
            '0',
            '1',
            NOW(),
            NOW(),
            (SELECT id FROM media WHERE original_name = 'skema_sympa-min.png')
        )");

        $this->addSql("UPDATE bloc SET content = '<p>Rendez-vous <strong>le %parameter.dateResultatsAdmissibilite%</strong> pour consulter les résultats d’admissibilité !</p>' WHERE tag_id = (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_VALIDATED_CANDIDACY')");
        $this->addSql("UPDATE bloc SET content = '<p>Vous êtes admissible aux épreuves orales du Concours.<br />Vous avez <strong>jusqu’au %parameter.dateFermetureRDV% pour choisir une date et un campus</strong> pour passer vos épreuves orales (en fonction des places disponibles)</p>' WHERE tag_id = (SELECT id FROM bloc_tag WHERE label = 'DASHBOARD_ADMISSIBLE_CANDIDACY')");
    }

    public function down(Schema $schema): void
    {

    }
}
