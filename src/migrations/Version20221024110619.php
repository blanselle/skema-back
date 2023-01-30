<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024110619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Update Exam classification name for TOEIC';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE exam_classification SET 
            name = 'TOEIC®' 
            WHERE name = 'TOIEC®';
        ");

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
                    (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_MESSAGES'), 
                    NULL,
                    'STUDENT_EXPORT_LIST_NOTIFICATION', 
                    'Export terminé.', 
                    'Votre export est terminé: <a href=\"%link%\">Cliquez ici pour téléchager votre fichier</a>', 
                    NULL, 
                    NULL, 
                    '1', 
                    '1', 
                    now(), 
                    now())"
        );

    }

    public function down(Schema $schema): void {}
}
