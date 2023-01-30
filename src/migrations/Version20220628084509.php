<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628084509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate data for TAGE 2';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,0,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,1,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,2,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,3,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,4,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,6,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,7,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,8,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,9,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,10,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,11,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,12,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,13,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,14,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,15,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,16,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,17,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,18,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,19,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,20,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,21,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,22,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,23,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,24,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,25,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,26,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,27,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,28,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,29,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,30,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,31,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,32,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,33,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,34,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,35,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,36,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,37,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,38,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,39,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,40,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,41,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,42,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,43,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,44,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,45,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,46,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,47,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,48,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,49,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,50,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,51,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,52,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,53,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,54,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,55,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,56,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,57,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,58,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,59,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,60,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,61,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,62,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,63,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,64,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,65,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,66,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,67,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,68,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,69,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,70,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,71,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,72,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,73,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,74,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,75,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,76,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,77,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,78,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,79,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,80,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,81,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,82,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,83,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,84,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,85,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,86,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,87,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,88,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,89,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,90,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,91,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,92,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,93,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,94,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,95,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,96,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,97,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,98,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,99,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,100,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,101,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,102,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,103,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,104,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,105,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,106,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,107,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,108,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,109,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,110,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,111,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,112,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,113,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,114,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,115,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,116,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,117,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,118,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,119,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,120,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,121,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,122,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,123,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,124,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,125,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,126,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,127,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,128,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,129,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,130,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,131,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,132,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,133,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,134,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,135,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,136,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,137,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,138,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,139,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,140,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,141,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,142,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,143,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,144,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,145,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,146,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,147,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,148,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,149,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,150,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,151,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,152,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,153,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,154,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,155,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,156,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,157,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,158,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,159,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,160,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,161,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,162,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,163,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,164,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,165,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,166,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,167,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,168,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,169,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,170,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,171,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,172,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,173,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,174,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,175,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,176,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,177,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,178,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,179,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,180,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,181,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,182,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,183,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,184,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,185,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,186,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,187,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,188,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,189,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,190,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,191,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,192,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,193,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,194,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,195,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,196,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,197,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,198,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,199,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,200,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,201,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,202,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,203,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,204,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,205,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,206,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,207,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,208,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,209,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),4,210,NOW(),NOW())");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('delete from exam_classification_score where exam_classification_id = 4');
    }
}
