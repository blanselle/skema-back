<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220616081913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Insert bloc check_diploma';
    }

    public function up(Schema $schema): void
    {
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
            (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_MESSAGES'),	
            'Compléter votre dossier de candidature',	
            'Bonjour %firstname%, votre diplôme est éligible au concours du programme Grande École de Skema. Vous pouvez dès à présent compléter votre dossier administratif et payer vos frais de concours. Le service concours',
            NULL,
            NULL,
            1,
            '1',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39',
            'NOTIFICATION_CHECK_DIPLOMA_TO_CREATED',
            NULL
        );");

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
            (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_MESSAGES'),
            'Votre dossier de candidature est rejetée',	
            'Bonjour %firstname%, votre dossier de candidature a été rejeté, vous n’êtes pas éligible au concours du programme Grande École de Skema. Le service concours', 
            NULL,
            NULL,
            1,
            '1',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39',
            'NOTIFICATION_CHECK_DIPLOMA_TO_REJECTED_DIPLOMA',
            NULL
        );");
    }        

    public function down(Schema $schema): void
    {
    }
}
