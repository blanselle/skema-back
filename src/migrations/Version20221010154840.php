<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221010154840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Payment]';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE "order_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "order" (id INT NOT NULL, student_id INT NOT NULL, exam_session_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, amount INT NOT NULL, state VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5299398CB944F1A ON "order" (student_id)');
        $this->addSql('CREATE INDEX IDX_F52993987337968A ON "order" (exam_session_id)');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F52993987337968A FOREIGN KEY (exam_session_id) REFERENCES exam_session (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_student DROP paid');

        $this->addSql('ALTER TABLE payment DROP CONSTRAINT fk_6d28840dcb944f1a');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT fk_6d28840dd99c354f');
        $this->addSql('DROP INDEX uniq_6d28840dd99c354f');
        $this->addSql('DROP INDEX idx_6d28840dcb944f1a');
        $this->addSql('ALTER TABLE payment ADD mode VARCHAR(255) DEFAULT \'online\' NOT NULL');
        $this->addSql('ALTER TABLE payment ADD additional_information TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment DROP exam_student_id');
        $this->addSql('ALTER TABLE payment DROP type');
        $this->addSql('ALTER TABLE payment DROP amount');
        $this->addSql('ALTER TABLE payment RENAME COLUMN student_id TO indent_id ');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DAF378502 FOREIGN KEY (indent_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6D28840DAF378502 ON payment (indent_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DAF378502');
        $this->addSql('DROP SEQUENCE "order_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F5299398CB944F1A');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F52993987337968A');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP INDEX IDX_6D28840DAF378502');
        $this->addSql('ALTER TABLE payment ADD exam_student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payment ADD amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment DROP mode');
        $this->addSql('ALTER TABLE payment DROP additional_information');
        $this->addSql('ALTER TABLE payment RENAME COLUMN indent_id TO student_id');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_6d28840dcb944f1a FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_6d28840dd99c354f FOREIGN KEY (exam_student_id) REFERENCES exam_student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840dd99c354f ON payment (exam_student_id)');
        $this->addSql('CREATE INDEX idx_6d28840dcb944f1a ON payment (student_id)');
        $this->addSql('ALTER TABLE exam_student ADD paid BOOLEAN DEFAULT NULL');
    }
}
