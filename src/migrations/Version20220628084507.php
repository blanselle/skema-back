<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628084507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate data for TOIEC';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,0,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,5,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,10,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,15,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,20,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,25,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,30,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,35,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,40,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,45,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,50,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,55,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,60,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,65,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,70,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,75,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,80,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,85,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,90,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,95,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,100,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,105,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,110,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,115,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,120,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,125,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,130,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,135,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,140,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,145,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,150,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,155,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,160,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,165,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,170,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,175,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,180,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,185,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,190,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,195,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,200,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,205,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,210,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,215,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,220,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,225,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,230,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,235,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,240,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,245,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,250,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,255,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,260,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,265,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,270,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,275,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,280,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,285,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,290,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,295,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,300,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,305,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,310,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,315,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,320,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,325,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,330,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,335,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,340,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,345,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,350,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,355,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,360,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,365,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,370,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,375,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,380,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,385,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,390,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,395,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,400,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,405,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,410,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,415,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,420,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,425,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,430,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,435,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,440,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,445,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,450,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,455,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,460,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,465,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,470,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,475,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,480,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,485,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,490,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,495,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,500,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,505,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,510,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,515,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,520,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,525,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,530,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,535,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,540,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,545,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,550,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,555,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,560,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,565,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,570,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,575,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,580,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,585,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,590,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,595,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,600,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,605,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,610,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,615,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,620,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,625,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,630,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,635,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,640,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,645,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,650,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,655,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,660,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,665,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,670,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,675,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,680,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,685,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,690,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,695,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,700,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,705,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,710,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,715,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,720,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,725,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,730,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,735,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,740,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,745,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,750,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,755,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,760,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,765,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,770,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,775,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,780,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,785,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,790,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,795,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,800,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,805,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,810,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,815,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,820,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,825,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,830,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,835,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,840,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,845,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,850,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,855,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,860,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,865,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,870,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,875,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,880,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,885,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,890,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,895,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,900,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,905,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,910,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,915,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,920,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,925,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,930,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,935,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,940,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,945,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,950,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,955,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,960,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,965,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,970,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,975,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,980,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,985,NOW(),NOW())");
        $this->addSql("insert into exam_classification_score (id, exam_classification_id, score, created_at, updated_at) values (nextval('exam_classification_score_id_seq'),1,990,NOW(),NOW())");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('delete from exam_classification_score where exam_classification_id = 1');
    }
}
