<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221118144617 extends AbstractMigration
{
    private const PROGRAM_CHANNELS = ['ast1', 'ast2', 'bce_eco', 'bce_lit'];
    private const PARAMETERS = [
        'dateDebutSession3Anglais',
        'dateDebutSession4Anglais',
        'dateDebutSession5Anglais',
        'dateDebutSession6Anglais',
        'dateFinSession1Anglais',
        'dateFinSession2Anglais',
        'dateFinSession3Anglais',
        'dateFinSession4Anglais',
        'dateFinSession5Anglais',
        'dateFinSession6Anglais',
        'dateFinSession1Management',
        'dateFinSession2Management',
    ];

    public function getDescription(): string
    {
        return 'Update and add new parameters';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE parameter_key as p set name = 'dateDebutSession1Anglais' WHERE p.name = 'dateSession1Anglais'");
        $this->addSql("UPDATE parameter_key as p set name = 'dateDebutSession2Anglais' WHERE p.name = 'dateSession2Anglais'");
        $this->addSql("UPDATE parameter_key as p set name = 'dateDebutSession1Management' WHERE p.name = 'dateSession1Management'");
        $this->addSql("UPDATE parameter_key as p set name = 'dateDebutSession2Management' WHERE p.name = 'dateSession2Management'");

        foreach(self::PARAMETERS as $name) {
            $this->addParameterKey($name, 'YYYY-MM-DD');
            $this->addParameter($name, '10/04/2023', self::PROGRAM_CHANNELS);
        }
    }

    public function down(Schema $schema): void
    {

    }

    private function addParameter(string $key, string $value, array $programChannels): void {
        $this->addSql("INSERT INTO parameters (
            id,
            key_id,
            value_date_time,
            created_at,
            updated_at
        ) VALUES (
            nextval('parameters_id_seq'),
            (SELECT id FROM parameter_key WHERE name = :key),
            :value,
            NOW(),
            NOW()
        )", [
            'value' => $value,
            'key' => $key,
        ]);

        foreach($programChannels as $programChannel) {
            $this->addSql("INSERT INTO parameter_program_channel (
                parameter_id,
                program_channel_id
            ) VALUES (
                currval('parameters_id_seq'),
                (SELECT id FROM program_channel WHERE key = :key)
            )", [
                'key' => $programChannel
            ]);
        }
    }

    private function addParameterKey(string $name, string $format): void {
        $this->addSql("INSERT INTO parameter_key (
            id,
            name,
            type,
            format,
            created_at,
            updated_at
        ) VALUES (
            nextval('parameter_key_id_seq'),
            :name,
            'date',
            :format,
            NOW(),
            NOW()
        )", [
            'name' => $name,
            'format' => $format,
        ]);
    }
}
