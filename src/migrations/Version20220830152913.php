<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220830152913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Insert bloc message email already exists';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc (
            id, 
            tag_id, 
            media_id,
            key,
            label, 
            content, 
            link, 
            label_link, 
            position, 
            active, 
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_id_seq'),
            (select id from bloc_tag where label = 'ERRORS'),	
            NULL,	
            'MESSAGE_EMAIL_ALREADY_EXISTS',
            NULL,
            'L’adresse email est déjà lié a un compte, merci de vous connectez', 
            NULL,
            NULL,
            1,
            '1',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39'
        );");
    }

    public function down(Schema $schema): void
    {
    }
}
