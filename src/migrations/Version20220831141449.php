<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220831141449 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE exam_language_program_channel (exam_language_id INT NOT NULL, program_channel_id INT NOT NULL, PRIMARY KEY(exam_language_id, program_channel_id))');
        $this->addSql('CREATE INDEX IDX_6D5F36FE99E217F5 ON exam_language_program_channel (exam_language_id)');
        $this->addSql('CREATE INDEX IDX_6D5F36FE64CF5C1E ON exam_language_program_channel (program_channel_id)');
        $this->addSql('ALTER TABLE exam_language_program_channel ADD CONSTRAINT FK_6D5F36FE99E217F5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_language_program_channel ADD CONSTRAINT FK_6D5F36FE64CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_channel DROP CONSTRAINT fk_2913769399e217f5');
        $this->addSql('DROP INDEX idx_2913769399e217f5');
        $this->addSql('ALTER TABLE program_channel DROP exam_language_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE exam_language_program_channel');
        $this->addSql('ALTER TABLE program_channel ADD exam_language_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program_channel ADD CONSTRAINT fk_2913769399e217f5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2913769399e217f5 ON program_channel (exam_language_id)');
    }
}
