<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628084511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate data for ICIMS';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,0,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,1,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,2,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,3,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,4,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,6,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,7,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,8,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,9,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,10,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,11,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,12,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,13,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,14,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,15,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,16,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,17,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,18,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,19,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,20,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,21,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,22,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,23,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,24,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,25,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,26,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,27,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,28,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,29,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,30,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,31,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,32,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,33,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,34,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,35,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,36,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,37,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,38,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,39,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,40,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,41,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,42,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,43,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,44,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,45,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,46,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,47,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,48,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,49,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,50,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,51,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,52,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,53,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,54,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,55,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,56,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,57,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,58,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,59,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,60,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,61,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,62,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,63,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,64,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,65,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,66,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,67,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,68,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,69,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,70,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,71,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,72,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,73,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,74,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,75,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,76,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,77,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,78,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,79,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,80,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,81,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,82,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,83,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,84,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,85,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,86,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,87,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,88,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,89,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,90,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,91,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,92,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,93,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,94,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,95,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,96,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,97,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,98,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,99,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),3,100,NOW(),NOW())");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('delete from exam_classification_score where exam_classification_id = 3');
    }
}
