<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830113725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus ADD assignment_campus BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE campus ADD oral_test_center BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE campus ADD contest_jury_website_code VARCHAR(255) DEFAULT NULL');

        // update existing data
        $this->addSql("UPDATE campus SET assignment_campus = true, oral_test_center = true, contest_jury_website_code = 'paris' WHERE campus.name LIKE '%Paris'");
        $this->addSql("UPDATE campus SET assignment_campus = true, oral_test_center = true, contest_jury_website_code = 'lille' WHERE campus.name LIKE '%Lille'");
        $this->addSql("UPDATE campus SET assignment_campus = true, oral_test_center = true, contest_jury_website_code = 'sophia' WHERE campus.name LIKE '%Sophia%'");
        $this->addSql("INSERT INTO campus (id, name, address_line1, address_line2, postal_code, city, country, email, phone_reception, phone_customer_service, created_at, updated_at, assignment_campus, oral_test_center, contest_jury_website_code) VALUES (nextval('campus_id_seq'), 'Distance', '5 Quai Marcel Dassault', 'CS 90067', '92156', 'Suresnes Cedex', 'France', 'service.concours@skema.edu', '+33 (0)1 71 13 39 01', '+33 (0)1 87 10 08 27', '2022-08-16 00:00:00', '2022-08-16 00:00:00', false, true, 'distance')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE campus DROP assignment_campus');
        $this->addSql('ALTER TABLE campus DROP oral_test_center');
        $this->addSql('ALTER TABLE campus DROP contest_jury_website_code');
    }
}
