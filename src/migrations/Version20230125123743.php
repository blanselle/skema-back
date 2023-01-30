<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230125123743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Bloc Update bloc RESIGNATION_NOTIFICATION';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc set label='Démission par le service concours', content='<p>Bonjour %firstname%,</p> <p>Votre candidature pour le concours PGE Skema a été annulé pour le motif suivant : %motif%.</p> <p>Le Service Concours</p>', tag_id=(select id from bloc_tag where label='NOTIFICATION_MESSAGES') where key='RESIGNATION_NOTIFICATION'");
    }

    public function down(Schema $schema): void
    {
    }
}
