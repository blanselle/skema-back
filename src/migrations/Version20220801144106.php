<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220801144106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Missing migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac_sup ALTER type DROP DEFAULT');
        $this->addSql('ALTER TABLE exam_session ALTER type DROP DEFAULT');
        $this->addSql('ALTER TABLE exam_student ALTER specific DROP DEFAULT');
        $this->addSql('ALTER TABLE exam_student ALTER confirmed DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac_sup ALTER type SET DEFAULT \'annuel\'');
        $this->addSql('ALTER TABLE exam_session ALTER type SET DEFAULT \'Skema\'');
        $this->addSql('ALTER TABLE exam_student ALTER specific SET DEFAULT false');
        $this->addSql('ALTER TABLE exam_student ALTER confirmed SET DEFAULT false');
    }
}
