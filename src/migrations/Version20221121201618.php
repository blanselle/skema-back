<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221121201618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Ranking] add error bloc';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_RANKING_MESSAGES'), NULL, 'RANKING_ADMISSIBILITY_NOTIFICATION_ERROR', 'La simulation a échouée.', '<p>Une erreur est survenue lors de la simulation du ranking.</p><p>Essayez de relancer une simulation ou contactez votre administrateur.</p>', NULL, NULL, '1', '1', now(), now())");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
