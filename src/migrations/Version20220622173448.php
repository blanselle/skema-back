<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220622173448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into bac_option_bac_type (bac_type_id, bac_option_id) values ((select id from bac_type where name = 'Arts'), (select id from bac_option where name = 'Arts du cirque'))");
        $this->addSql("insert into bac_option_bac_type (bac_type_id, bac_option_id) values ((select id from bac_type where name = 'Arts'), (select id from bac_option where name = 'Arts plastiques'))");
        $this->addSql("insert into bac_option_bac_type (bac_type_id, bac_option_id) values ((select id from bac_type where name = 'Arts'), (select id from bac_option where name = 'Cinéma - Audiovisuel'))");
        $this->addSql("insert into bac_option_bac_type (bac_type_id, bac_option_id) values ((select id from bac_type where name = 'Arts'), (select id from bac_option where name = 'Danse'))");
        $this->addSql("insert into bac_option_bac_type (bac_type_id, bac_option_id) values ((select id from bac_type where name = 'Arts'), (select id from bac_option where name = 'Musique'))");
        $this->addSql("insert into bac_option_bac_type (bac_type_id, bac_option_id) values ((select id from bac_type where name = 'Arts'), (select id from bac_option where name = 'Théâtre'))");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('truncate table bac_option_bac_type');
    }
}
