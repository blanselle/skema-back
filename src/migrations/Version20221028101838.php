<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221028101838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[SCHEMAS] Add delete cascade on media';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CCB944F1A');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE media DROP CONSTRAINT fk_6a2ca10ccb944f1a');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT fk_6a2ca10ccb944f1a FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
