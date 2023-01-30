<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220923135649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Remove exam summon on exterieur session';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM exam_summon WHERE id IN (
            SELECT esum.id
            FROM exam_summon esum
            LEFT JOIN exam_session es ON esum.exam_session_id = es.id
            WHERE es.type = \'Extérieur\'
        )');
    }

    public function down(Schema $schema): void
    {
    }
}
