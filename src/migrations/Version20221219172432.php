<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221219172432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Edit bloc writtenTestSummonMessage';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc 
            SET content = '<h1><u>CONVOCATION</u></h1><br /><h2>CONCOURS D’ADMISSION SUR TITRES SKEMA</h2><br /><p style=text-align:left;>Nous vous invitons à vous présenter à <strong>l’épreuve écrite d’admissibilité</strong> :<br /><h3>%default.typologie% le %default.exam_date_start%</h3></p>' 
            WHERE (key = 'SUMMONS_ZONE_1')
        ");
        $this->addSql("UPDATE bloc 
            SET content = 'Bonjour %default.firstname%, votre convocation pour l’épreuve %default.nom_typologie% est téléchargeable dans la rubrique « Mes épreuves écrites ». Le service concours' 
            WHERE (key = 'NOTIFICATION_SUMMONS_GENERATED')
        ");
    }

    public function down(Schema $schema): void
    {
    }
}
