<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920121549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Admissibility calculator new fields + new blocs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM admissibility_calculator');
        $this->addSql('ALTER TABLE admissibility_calculator ADD user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE admissibility_calculator ADD type VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE admissibility_calculator ADD last_launch_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN admissibility_calculator.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE admissibility_calculator ADD CONSTRAINT FK_B904D8F9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B904D8F9A76ED395 ON admissibility_calculator (user_id)');

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'NOTIFICATION_RANKING_MESSAGES', now(), now());");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_RANKING_MESSAGES'), NULL, 'RANKING_ADMISSIBILITY_NOTIFICATION', 'La définition des admissibles sera terminée.', 'La définition des admissibles sera terminée.', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_RANKING_MESSAGES'), NULL, 'RANKING_SIMULATOR_NOTIFICATION', 'La simulation de ranking est terminée.', 'La simulation de ranking admissibilité est terminée.', NULL, NULL, '1', '1', now(), now())");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admissibility_calculator DROP CONSTRAINT FK_B904D8F9A76ED395');
        $this->addSql('DROP INDEX IDX_B904D8F9A76ED395');
        $this->addSql('ALTER TABLE admissibility_calculator DROP user_id');
        $this->addSql('ALTER TABLE admissibility_calculator DROP type');
        $this->addSql('ALTER TABLE admissibility_calculator DROP last_launch_date');
    }
}
