<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220608150633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Insert bloc mail reject';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (
            id, 
            label, 
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_tag_id_seq'),
            'MAIL_REJECT',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39'
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
            32,	
            'Votre demande de dérogation est rejetée',	
            'Bonjour %firstname%, votre demande de dérogation a été rejetée, vous n’êtes pas éligible au concours du programme Grande École de Skema. Le service concours', 
            NULL,
            NULL,
            1,
            '1',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39',
            'MAIL_REJECT',
            NULL
        );");
    }        

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM bloc WHERE id LIKE 73');
        $this->addSql('DELETE FROM bloc_tag WHERE id LIKE 32');
    }
}
