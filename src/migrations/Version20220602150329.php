<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220602150329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Insert tags on bac_types';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM bac_type');
        $this->addSql("INSERT INTO bac_type (id, bac_channel_id, name, tags) VALUES
            (nextval('bac_type_id_seq'), 3,	'Histoire-géographie, géopolitique et sciences politiques',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Scientifique',	'a:1:{i:0;s:1:\"1\";}'),
            (nextval('bac_type_id_seq'), 3,	'Littéraire',	'a:1:{i:0;s:1:\"1\";}'),
            (nextval('bac_type_id_seq'), 3,	'Économique et social',	'a:1:{i:0;s:1:\"1\";}'),
            (nextval('bac_type_id_seq'), 3,	'Arts',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Biologie',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Humanités, littérature et philosophie',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Littérature et langues et cultures de l''Antiquité',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Langues, littératures et cultures étrangères et régionales',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Mathématiques',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Numérique et sciences informatiques',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Physique-chimie',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Sciences économiques et sociales',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Sciences de l’Ingénieur',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 3,	'Sciences de la Vie et de la Terre',	'a:1:{i:0;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 1,	'ST2S',	'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 1,	'STL',	'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 1,	'STD2A',	'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 1,	'STI2D',	'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 1,	'STMG',	'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 1,	'STHR',	'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}'),
            (nextval('bac_type_id_seq'), 1,	'S2TMD',	'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}')
        ");
    }
}
