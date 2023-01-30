<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220728152543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change type score and score_retained to float';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE school_report ALTER score TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE school_report ALTER score DROP DEFAULT');
        $this->addSql('ALTER TABLE school_report ALTER score_retained TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE school_report ALTER score_retained DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE school_report ALTER score TYPE INT');
        $this->addSql('ALTER TABLE school_report ALTER score DROP DEFAULT');
        $this->addSql('ALTER TABLE school_report ALTER score_retained TYPE INT');
        $this->addSql('ALTER TABLE school_report ALTER score_retained DROP DEFAULT');
    }
}
