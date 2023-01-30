<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221219172440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Abandoned media recovery';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE media
        SET 
            state = 'cancelled',
            updated_at = NOW()
        WHERE media.id IN (
        SELECT m.id
        FROM media m
        LEFT JOIN student s ON m.student_id = s.id
        WHERE (m.state = 'to_check' OR m.state = 'uploaded')
          AND EXISTS (SELECT 1 FROM media WHERE student_id = s.id AND state = 'accepted' AND code = m.code)
        )
        AND (media.code = 'attestation_anglais' OR media.code = 'bac')");
    }

    public function down(Schema $schema): void
    {
    }
}
