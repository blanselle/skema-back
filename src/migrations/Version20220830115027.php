<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830115027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_language ADD color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_language ADD key VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE program_channel ADD exam_language_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program_channel ADD CONSTRAINT FK_2913769399E217F5 FOREIGN KEY (exam_language_id) REFERENCES exam_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2913769399E217F5 ON program_channel (exam_language_id)');

        $this->addSql("UPDATE exam_language SET color = '#41D653', key = 'ALL' WHERE name = 'Allemand';");
        $this->addSql("UPDATE exam_language SET color = '#C70039', key = 'ESP' WHERE name = 'Espagnol';");
        $this->addSql("UPDATE exam_language SET color = '#FF5733', key = 'CHI' WHERE name = 'Chinois';");
        $this->addSql("UPDATE exam_language SET color = '#4BE4E7', key = 'ARA' WHERE name = 'Arabe';");
        $this->addSql("UPDATE exam_language SET color = '#4B81E7', key = 'ITA' WHERE name = 'Italien';");
        $this->addSql("UPDATE exam_language SET color = '#570C0C', key = 'RUS' WHERE name = 'Russe';");

        $this->addSql("INSERT INTO exam_language (id, name, key, color, created_at, updated_at) VALUES (nextval('exam_language_id_seq'),'Grec','GRC','#756C79',now(),now()), (nextval('exam_language_id_seq'),'Hébreu','HEB','#C19B22',now(),now()), (nextval('exam_language_id_seq'),'Japonais','JAP','#EE3EEE',now(),now()), (nextval('exam_language_id_seq'),'Latin','LAT','#7F147F',now(),now()), (nextval('exam_language_id_seq'),'Polonais','POL','#F189C6',now(),now()), (nextval('exam_language_id_seq'),'Portugais','POR','#AFD3A9',now(),now()), (nextval('exam_language_id_seq'),'Turc','TUR','#91A812',now(),now()), (nextval('exam_language_id_seq'),'Vietnamien','VIE','#7A6969',now(),now()), (nextval('exam_language_id_seq'),'Anglais','ANG','#FFE633',now(),now())");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exam_language DROP color');
        $this->addSql('ALTER TABLE exam_language DROP key');
        $this->addSql('ALTER TABLE program_channel DROP CONSTRAINT FK_2913769399E217F5');
        $this->addSql('DROP INDEX IDX_2913769399E217F5');
        $this->addSql('ALTER TABLE program_channel DROP exam_language_id');
    }
}
