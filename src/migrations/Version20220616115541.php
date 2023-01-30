<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220616115541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMAS] bloc profil';
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
            'PROFIL_VERBATIM',
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
            (SELECT id FROM bloc_tag WHERE label = 'PROFIL_VERBATIM'),
            NULL,
            'Lorem ipsum dolor sit amet', 
            NULL,
            NULL,
            1,
            '1',
            '2022-06-02 11:25:39',
            '2022-06-02 11:25:39',
            NULL,
            NULL
        );");
    } 

    public function down(Schema $schema): void
    {
    }
}
