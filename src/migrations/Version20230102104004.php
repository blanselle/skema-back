<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102104004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        //
        $this->addSql("INSERT INTO parameter_key (id, name, type, format, description, created_at, updated_at) VALUES (nextval('parameter_key_id_seq'),'limiteRDV','number',NULL,'Limite à partir de laquelle les créneaux aux épreuves orales sont disponibles',NOW(),NOW())");
        $this->addSql("INSERT INTO parameters (id, key_id, value_string, value_date_time, value_number, created_at, updated_at) VALUES (nextval('parameters_id_seq'),(SELECT id FROM parameter_key WHERE name = 'limiteRDV'),NULL,NULL,2,NOW(),NOW())");

        $this->addSql("INSERT INTO parameter_campus (parameter_id, campus_id)
            SELECT  (SELECT MAX(id) FROM parameters), c.id
            FROM (SELECT id FROM campus WHERE assignment_campus=true and oral_test_center=true) c
        ");
        $this->addSql("INSERT INTO parameter_program_channel (parameter_id, program_channel_id)
            SELECT  (SELECT MAX(id) FROM parameters), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");
    }

    public function down(Schema $schema): void
    {
        // empty
    }
}
