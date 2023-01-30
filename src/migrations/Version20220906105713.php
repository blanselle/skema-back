<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220906105713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Insert bloc message notification verbatim';
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
            'NOTIFICATION_VERBATIM',
            NOW(),
            NOW()
        )");

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
            (select id from bloc_tag where label = 'NOTIFICATION_VERBATIM'),	
            NULL,	
            NULL,
            NULL,
            'Retrouvez ici tous les messages envoyés par le service concours', 
            NULL,
            NULL,
            1,
            '1',
            now(),
            now()
        );");
    }

    public function down(Schema $schema): void
    {
    }
}
