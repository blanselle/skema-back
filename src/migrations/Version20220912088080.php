<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version2022091208080 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Fix bloc ERROR_REINIT_PASSWORD';
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
            'ERROR_REINIT_PASSWORD',
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO bloc_tag (
            id, 
            label, 
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_tag_id_seq'),
            'MSG_REINIT_PASSWORD_SUCCESS',
            NOW(),
            NOW()
        )");

        $this->addSql("UPDATE bloc SET 
            tag_id = (SELECT id FROM bloc_tag WHERE label = 'ERROR_REINIT_PASSWORD')
            WHERE bloc.key = 'ERROR_REINIT_PASSWORD'"
        );

        $this->addSql("UPDATE bloc SET 
            tag_id = (SELECT id FROM bloc_tag WHERE label = 'MSG_REINIT_PASSWORD_SUCCESS')
            WHERE bloc.key = 'MSG_REINIT_PASSWORD_SUCCESS'"
        );
    }
    
    public function down(Schema $schema): void
    {
    }
}
