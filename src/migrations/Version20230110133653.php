<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230110133653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create bloc with tag "PAYMENT "and key "MSG_PAYMENT_WAITNG" and add "paymentModalTimeout" parameter';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc (
            id,
            tag_id,
            key,
            label,
            content,
            position,
            active,
            created_at,
            updated_at
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'PAYMENT'),
            'MSG_PAYMENT_WAITNG',
            null,
            '<p>Merci de patienter, vous aller être redirigé sur le site concours Skema.</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO parameter_key (
            id,
            name,
            type,
            format,
            description,
            created_at,
            updated_at
        ) VALUES (
            nextval('parameter_key_id_seq'),
            'paymentModalTimeout',
            'number',
            NULL,
            'Délai d’affichage de la modal (en secondes)',
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO parameters (
            id,
            key_id,
            value_string,
            value_date_time,
            value_number,
            created_at,
            updated_at
        ) VALUES (
            nextval('parameters_id_seq'),
            (SELECT id FROM parameter_key WHERE name = 'paymentModalTimeout'),
            NULL,
            NULL,
            3,
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id)
            SELECT  (SELECT MAX(id) FROM bloc), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");

        $this->addSql("INSERT INTO parameter_program_channel (parameter_id, program_channel_id)
            SELECT (SELECT MAX(id) FROM parameters), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");
    }

    public function down(Schema $schema): void
    {

    }
}
