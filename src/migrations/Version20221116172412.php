<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221116172412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[PAYMENT] Add label to bloc with tag PAYMENT';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc as b set label='Votre paiement' FROM bloc_tag as bt WHERE bt.id=b.tag_id AND bt.label='PAYMENT'");
    }

    public function down(Schema $schema): void
    {
    }
}
