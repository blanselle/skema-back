<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220602085920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert service concours user and bloc notification checkdiploma';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO users (
            id, 
            email, 
            roles, 
            password, 
            student_id, 
            first_name, 
            last_name, 
            created_at, 
            updated_at, 
            plain_password
        ) VALUES (
            '1ece2524-e10d-6be8-8bcd-556800179a58', 
            'service.concours@skema.edu', 
            'a:1:{i:0;s:10:\"ROLE_ADMIN\";}', 
            '', 
            NULL,
            'Service', 
            'Councours', 
            now(), 
            now(), 
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
            30,	
            'Votre candidature est en attente',	
            'Bonjour %firstname%, l’éligibilité de votre candidature va être contrôlée. Vous pouvez compléter votre dossier administratif. Le service concours', 
            NULL,
            NULL,
            1,
            '1',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39',
            'NOTIFICATION_CREATE_STUDENT',
            NULL
        );");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM users WHERE id LIKE "1ece2524-e10d-6be8-8bcd-556800179a58"');
        $this->addSql('DELETE FROM bloc WHERE id LIKE 74');
    }
}
