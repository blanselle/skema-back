<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220602124159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert bloc notification derogation valid';
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
            30,	
            'Votre compte est activé',	
            'Bonjour %firstname%, votre demande de dérogation est validée, votre compte est désormais activé et vous pouvez vous connecter sur votre espace afin de compléter votre dossier de candidature. Le service concours', 
            NULL,
            NULL,
            1,
            '1',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39',
            'NOTIFICATION_DEROGATION_VALIDATED',
            NULL
        );");
    }        

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM bloc WHERE id LIKE 75');
    }
}
