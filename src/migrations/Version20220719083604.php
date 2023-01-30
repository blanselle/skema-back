<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220719083604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ExamSession : Rename field fneige to fnege';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session RENAME COLUMN fneige TO fnege');
        $this->addSql("INSERT INTO bloc_tag (
            id, 
            label, 
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_tag_id_seq'),
            'NOTIFICATION_IMPORT_SCORES_DONE',
            NOW(),
            NOW()
        )");
        $this->addSql("INSERT INTO bloc (
            id, 
            tag_id, 
            label, 
            content, 
            link, 
            label_link, 
            position, 
            active, 
            created_at, 
            updated_at, 
            key, 
            media_id
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_IMPORT_SCORES_DONE'),	
            'Import de notes effectué',	
            'Bonjour, l’import de notes pour la session %exam_classification% a bien été effectuée, %nb_lines% lignes ont été traitées. Vous trouverez ci-après les erreurs éventuelles : %errors%',
            NULL,
            NULL,
            1,
            '1',
            NOW(),
            NOW(),
            'NOTIFICATION_IMPORT_SCORES_DONE',
            NULL
        )");

        $this->addSql("INSERT INTO bloc_tag (
            id, 
            label, 
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_tag_id_seq'),
            'NOTIFICATION_IMPORT_SCORES_NOTIFICATION_STUDENT',
            NOW(),
            NOW()
        )");
        $this->addSql("INSERT INTO bloc (
            id, 
            tag_id, 
            label, 
            content, 
            link, 
            label_link, 
            position, 
            active, 
            created_at, 
            updated_at, 
            key, 
            media_id
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_IMPORT_SCORES_NOTIFICATION_STUDENT'),	
            'Votre score %nom_typologie%',	
            'Bonjour %firstname%, votre score pour l’épreuve écrite %nom_typologie% a été complété sur votre dossier de candidature. Vous pouvez vous connecter sur votre espace, menu « Mes épreuves écrites » pour en prendre connaissance. Le service concours',
            NULL,
            NULL,
            1,
            '1',
            NOW(),
            NOW(),
            'NOTIFICATION_IMPORT_SCORES_NOTIFICATION_STUDENT',
            NULL
        )");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_session RENAME COLUMN fnege TO fneige');
    }
}
