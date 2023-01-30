<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628084514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate data for IETLS';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO exam_classification (id, exam_session_type_id, exam_condition_id, name, created_at, updated_at, need_confirmation) VALUES (nextval('exam_classification_id_seq'),1,1,'IETLS®',now(),now(),'0')");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),0,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),0.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),1,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),1.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),2,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),2.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),3,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),3.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),4,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),4.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),5.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),6,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),6.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),7,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),7.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),8,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),8.5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),(select id from exam_classification where name = 'IETLS®'),9,NOW(),NOW())");

        $this->addSql("insert into exam_classification_program_channel (exam_classification_id, program_channel_id) values ((select id from exam_classification where name = 'IETLS®'), 1)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("delete from exam_classification_score where exam_classification_id = (select id from exam_classification where name = 'IETLS®')");
        $this->addSql("delete from exam_classification_program_channel where exam_classification_id = (select id from exam_classification where name = 'IETLS®')");
        $this->addSql("delete from exam_classification where id = (select id from exam_classification where name = 'IETLS®')");
    }
}
