<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230124160455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Bloc Update NOTIFICATION_RANKING_MESSAGES';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc set label='La conversion des scores est terminée.', content='<p>La conversion des scores est terminée.</p>' where key='RANKING_ADMISSIBILITY_NOTIFICATION'");
        $this->addSql("UPDATE bloc set label='La conversion des scores a échouée.', content='<p>Une erreur est survenue lors de la conversion des scores.</p><p>Essayez de relancer une conversion ou contactez votre administrateur.</p>' where key='RANKING_ADMISSIBILITY_NOTIFICATION_ERROR'");
    }

    public function down(Schema $schema): void
    {
    }
}
