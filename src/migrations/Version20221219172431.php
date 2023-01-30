<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221219172431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATE] Add key on bloc REGISTRATIONS_CLOSED_CV_IN_PROGRESS_OF_CONTROL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE bloc SET key = 'REGISTRATIONS_CLOSED_CV_IN_PROGRESS_OF_CONTROL'
            WHERE bloc.id IN (
                SELECT b.id
                FROM bloc b
                LEFT JOIN bloc_tag bt ON bt.id = b.tag_id
                WHERE bt.label = 'REGISTRATIONS_CLOSED_CV_IN_PROGRESS_OF_CONTROL'
            )
        ");
    }

    public function down(Schema $schema): void
    {
    }
}
