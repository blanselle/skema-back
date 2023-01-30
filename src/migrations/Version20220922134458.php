<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220922134458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc set link = '/ast' from bloc_tag tag where bloc.tag_id = tag.id and bloc.label = '#AST' and tag.label = 'HOME_GE' ");

        $this->addSql("UPDATE program_channel SET key='bce_eco' WHERE name LIkE 'BCE Économique'");
        $this->addSql("UPDATE program_channel SET key='bce_lit' WHERE name LIkE 'BCE Littéraire'");
    }

    public function down(Schema $schema): void
    {
    }
}
