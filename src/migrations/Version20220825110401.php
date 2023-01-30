<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220825110401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix relation constraints student_diploma/administrative_record';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM student_diploma sd WHERE sd.administrative_record_id IS NULL');
        $this->addSql('ALTER TABLE administrative_record DROP CONSTRAINT FK_43E71B03CB944F1A');
        $this->addSql('ALTER TABLE administrative_record ADD CONSTRAINT FK_43E71B03CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_diploma DROP CONSTRAINT FK_1173CAFF35FC76B');
        $this->addSql('ALTER TABLE student_diploma ADD CONSTRAINT FK_1173CAFF35FC76B FOREIGN KEY (administrative_record_id) REFERENCES administrative_record (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9CB944F1A');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE administrative_record DROP CONSTRAINT fk_43e71b03cb944f1a');
        $this->addSql('ALTER TABLE administrative_record ADD CONSTRAINT fk_43e71b03cb944f1a FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e9cb944f1a');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT fk_1483a5e9cb944f1a FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_diploma DROP CONSTRAINT fk_1173caff35fc76b');
        $this->addSql('ALTER TABLE student_diploma ADD CONSTRAINT fk_1173caff35fc76b FOREIGN KEY (administrative_record_id) REFERENCES administrative_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
