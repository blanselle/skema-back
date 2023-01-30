<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221003101208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE payment (id INT NOT NULL, student_id INT NOT NULL, exam_student_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, amount INT DEFAULT NULL, redirect_url VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, merchant_reference UUID NOT NULL, external_payment_id VARCHAR(255) DEFAULT NULL, external_return_mac VARCHAR(255) DEFAULT NULL, external_hosted_checkout_id VARCHAR(255) DEFAULT NULL, external_status VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D1C79464F ON payment (merchant_reference)');
        $this->addSql('CREATE INDEX IDX_6D28840DCB944F1A ON payment (student_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840DD99C354F ON payment (exam_student_id)');
        $this->addSql('COMMENT ON COLUMN payment.merchant_reference IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DD99C354F FOREIGN KEY (exam_student_id) REFERENCES exam_student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE payment_id_seq CASCADE');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DCB944F1A');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DD99C354F');
        $this->addSql('DROP TABLE payment');
    }
}
