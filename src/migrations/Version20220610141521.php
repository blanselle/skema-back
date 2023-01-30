<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220610141521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Edit bloc with parameters';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc SET 
            content = 'Prenez soin de bien compléter chaque rubrique. Chaque diplôme, années d’études post bac et chaque expérience viendront valoriser votre candidature. Votre CV peut être complété en 1 ou plusieurs fois, mais il doit être validé avant le %parameter.dateFinCV%.' 
            WHERE id = 68;
        ");

        $this->addSql("UPDATE bloc SET 
            content = 'Votre candidature doit être validée avant le %parameter.dateClotureInscriptions%' 
            WHERE id = 65;
        ");
    }

    public function down(Schema $schema): void
    {
    }
}
