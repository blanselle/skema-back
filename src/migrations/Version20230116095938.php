<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230116095938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add admissibility_student_token and Insert bloc MAIL_RESULTAT_ADMISSIBILITE and Add bloc LANDING_ADMISSIBILITE';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'LANDING_ADMISSIBILITE', NOW(), NOW())");

        $this->addSql("INSERT INTO bloc (
            id,
            tag_id,
            key,
            content,
            position,
            active,
            created_at,
            updated_at
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'LANDING_ADMISSIBILITE'),
            'LANDING_ADMISSIBILITE',
            '<p>Les résultats d’admissibilité seront publiés le %parameter.dateResultatsAdmissibilite%</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id)
            SELECT  (SELECT MAX(id) FROM bloc), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");


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
            (SELECT id FROM bloc_tag WHERE label = 'MAIL'),
            'MAIL_RESULTAT_ADMISSIBILITE',
            'Accéder à vos résultats dès le %parameter.dateResultatsAdmissibilite%',
            '<p>Bonjour %default.firstname%, vos résultats d’admissibilité seront disponibles sur le lien suivant à partir du %parameter.dateResultatsAdmissibilite% : <a href=\"%default.link%\">%default.link%</a><br />
            Le service concours</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id)
            SELECT  (SELECT MAX(id) FROM bloc), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");

        $this->addSql('CREATE SEQUENCE admissibility_student_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_student_token (id INT NOT NULL, student_id INT NOT NULL, token VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E92A1AC6CB944F1A ON admissibility_student_token (student_id)');
        $this->addSql('ALTER TABLE admissibility_student_token ADD CONSTRAINT FK_E92A1AC6CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE SEQUENCE admissibility_purge_varnish_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admissibility_purge_varnish (id INT NOT NULL, program_channel_id INT DEFAULT NULL, state BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C42E6B964CF5C1E ON admissibility_purge_varnish (program_channel_id)');
        $this->addSql('ALTER TABLE admissibility_purge_varnish ADD CONSTRAINT FK_2C42E6B964CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX oral_test_oral_test_student_unique_index');
        $this->addSql('CREATE UNIQUE INDEX oral_test_oral_test_student_unique_index ON oral_test_oral_test_student (student_id) WHERE (state <> \'rejected\')');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE admissibility_student_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE admissibility_student_token DROP CONSTRAINT FK_E92A1AC6CB944F1A');
        $this->addSql('DROP TABLE admissibility_student_token');
        
        $this->addSql('DROP SEQUENCE admissibility_purge_varnish_id_seq CASCADE');
        $this->addSql('ALTER TABLE admissibility_purge_varnish DROP CONSTRAINT FK_2C42E6B964CF5C1E');
        $this->addSql('DROP TABLE admissibility_purge_varnish');
        $this->addSql('DROP INDEX oral_test_oral_test_student_unique_index');
        $this->addSql('CREATE UNIQUE INDEX oral_test_oral_test_student_unique_index ON oral_test_oral_test_student (student_id) WHERE ((state)::text <> \'rejected\'::text)');
    }
}
