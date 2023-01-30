<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201115406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrative_record DROP CONSTRAINT FK_43E71B03CB944F1A');
        $this->addSql('ALTER TABLE administrative_record ADD CONSTRAINT FK_43E71B03CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cv DROP CONSTRAINT FK_B66FFE92CB944F1A');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT FK_B66FFE92CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cv DROP CONSTRAINT fk_b66ffe92cb944f1a');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT fk_b66ffe92cb944f1a FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE administrative_record DROP CONSTRAINT fk_43e71b03cb944f1a');
        $this->addSql('ALTER TABLE administrative_record ADD CONSTRAINT fk_43e71b03cb944f1a FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
