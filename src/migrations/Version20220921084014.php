<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220921084014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] add bloc NOTIFICATION_JUSTIFICATIF_REJECTED';
    }
    
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc (
            id, 
            key,
            tag_id,
            content,
            position, 
            active,
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_id_seq'),
            'NOTIFICATION_JUSTIFICATIF_REJECTED',
            (SELECT id FROM bloc_tag WHERE label = 'ERRORS'),
            'Le justificatif %type% est refusé.',
            0,
            '1',
            NOW(),
            NOW()
        )");
    }
    
    public function down(Schema $schema): void
    {
    }
}
