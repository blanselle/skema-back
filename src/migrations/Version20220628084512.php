<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628084512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate data for TOEFL IBT';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("update exam_classification set name = 'TOEFL® IBT' where id=2");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,0,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,1,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,2,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,3,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,4,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,6,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,7,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,8,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,9,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,10,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,11,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,12,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,13,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,14,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,15,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,16,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,17,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,18,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,19,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,20,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,21,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,22,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,23,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,24,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,25,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,26,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,27,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,28,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,29,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,30,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,31,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,32,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,33,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,34,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,35,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,36,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,37,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,38,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,39,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,40,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,41,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,42,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,43,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,44,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,45,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,46,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,47,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,48,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,49,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,50,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,51,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,52,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,53,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,54,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,55,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,56,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,57,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,58,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,59,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,60,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,61,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,62,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,63,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,64,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,65,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,66,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,67,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,68,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,69,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,70,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,71,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,72,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,73,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,74,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,75,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,76,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,77,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,78,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,79,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,80,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,81,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,82,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,83,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,84,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,85,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,86,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,87,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,88,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,89,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,90,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,91,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,92,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,93,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,94,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,95,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,96,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,97,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,98,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,99,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,100,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,101,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,102,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,103,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,104,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,105,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,106,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,107,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,108,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,109,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,110,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,111,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,112,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,113,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,114,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,115,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,116,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,117,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,118,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,119,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),2,120,NOW(),NOW())");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('delete from exam_classification_score where exam_classification_id = 2');
    }
}
