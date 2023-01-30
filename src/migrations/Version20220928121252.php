<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220928121252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Fix bloc CV_POPIN_VALIDATION';
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
            'CV_POPIN_VALIDATION',
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO bloc (
            id,
            tag_id,
            key,
            label,
            content,
            position,
            active,
            created_at,
            updated_at
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'CV_POPIN_VALIDATION'),
            'CV_POPIN_VALIDATION',
            null,
            '<p>En soumettant mon CV, je confirme que :</p><ul><li><p>mes informations sont complètes</p></li><li><p>tous les justificatifs sont correctement téléchargés</p></li></ul><p>Je comprends que mon CV ne sera plus modifiable</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");
    }

    public function down(Schema $schema): void
    {
    }
}
