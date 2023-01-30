<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718073351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New blocs fixtures';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO media (id, file, original_name, created_at, updated_at, state, transition, type, code, student_id) VALUES (nextval('media_id_seq'),'fixtures/campus/icon_info.svg','icon_info1.svg',now(),now(),'uploaded','','image_cms','autre',NULL)");
        $this->addSql("INSERT INTO media (id, file, original_name, created_at, updated_at, state, transition, type, code, student_id) VALUES (nextval('media_id_seq'),'fixtures/campus/icon_info.svg','icon_info2.svg',now(),now(),'uploaded','','image_cms','autre',NULL)");
        $this->addSql("INSERT INTO media (id, file, original_name, created_at, updated_at, state, transition, type, code, student_id) VALUES (nextval('media_id_seq'),'fixtures/campus/icon_info.svg','icon_info3.svg',now(),now(),'uploaded','','image_cms','autre',NULL)");
        $this->addSql("INSERT INTO media (id, file, original_name, created_at, updated_at, state, transition, type, code, student_id) VALUES (nextval('media_id_seq'),'fixtures/campus/icon_voix.svg','icon_voix.svg',now(),now(),'uploaded','','image_cms','autre',NULL)");

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ADMINISTRATIVE_RECORD_LV2_INFOBULLE', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ADMINISTRATIVE_RECORD_DIPLOMA_DOUBLE_PARCOURS', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ADMINISTRATIVE_RECORD_INFO_HIGH_LEVEL_SPORTSMAN', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ADMINISTRATIVE_RECORD_SPORT_TEST_ARRANGEMENT', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'CV_PROFESSIONAL_EXPERIENCE', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'CV_INTERNATIONAL_EXPERIENCE', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'CV_ASSOCIATIVE_EXPERIENCE', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'WRITTEN_TEST_VERBATIM', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'WRITTEN_TEST_REMINDER', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'WRITTEN_TEST_ENGLISH_INFO', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'WRITTEN_TEST_MANAGEMENT_INFO', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ORAL_TEST_AST_VERBATIM', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ORAL_TEST_BCE_VERBATIM', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ORAL_TEST_AST_COMPLEMENTARY_INFO', now(), now())");
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'ORAL_TEST_BCE_COMPLEMENTARY_INFO', now(), now())");

        $this->addSql("UPDATE bloc SET media_id = (SELECT id FROM media WHERE original_name = 'icon_info1.svg') WHERE tag_id = (SELECT id FROM bloc_tag WHERE label = 'ADMINISTRATIVE_RECORD_LV2_PART')");

        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ADMINISTRATIVE_RECORD_LV2_INFOBULLE'), NULL, NULL, 'Lorem ipsum', 'Lorem ipsum', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ADMINISTRATIVE_RECORD_DIPLOMA_DOUBLE_PARCOURS'), NULL, NULL, 'Lorem ipsum', 'Lorem ipsum', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ADMINISTRATIVE_RECORD_INFO_HIGH_LEVEL_SPORTSMAN'), NULL, NULL, 'Lorem ipsum', 'Lorem ipsum', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ADMINISTRATIVE_RECORD_SPORT_TEST_ARRANGEMENT'), NULL, NULL, 'Lorem ipsum', 'Lorem ipsum', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'CV_PROFESSIONAL_EXPERIENCE'), NULL, NULL, 'Lorem ipsum', 'Lorem ipsum', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'CV_INTERNATIONAL_EXPERIENCE'), NULL, NULL, 'Lorem ipsum', 'Lorem ipsum', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'CV_ASSOCIATIVE_EXPERIENCE'), NULL, NULL, 'Lorem ipsum', 'Lorem ipsum', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'WRITTEN_TEST_VERBATIM'), NULL, NULL, '', 'Renseignez vos résultats aux tests de langue et management, ou inscrivez vous au session organisées par Skema sur les campus de Lille ou Sophia Antipolis (dans la limite des places disponibles)', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'WRITTEN_TEST_REMINDER'), (SELECT id FROM media WHERE original_name = 'icon_voix.svg'), NULL, '', '<strong>Pour rappel :</strong> Vos résultats du test de langue et de management devront être transmis au plus tard le %parameter.dateFinUploadEpreuveAnglais%', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'WRITTEN_TEST_ENGLISH_INFO'), NULL, NULL, '', 'Il appartient au candidat de veiller aux délais de correction du test, afin de pouvoir le fournir pour le %parameter.dateFinUploadEpreuveAnglais% (dernier délai). Tout retard ne sera pas accepté quel qu''en soit le motif (même si attestation non reçue pour des raisons indépendantes de la volonté du candidat)', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'WRITTEN_TEST_MANAGEMENT_INFO'), NULL, NULL, '', 'Il appartient au candidat de veiller aux délais de correction du test, afin de pouvoir le fournir pour le %parameter.dateFinUploadEpreuveGmat% (dernier délai). Tout retard ne sera pas accepté quel qu''en soit le motif (même si attestation non reçue pour des raisons indépendantes de la volonté du candidat)', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ORAL_TEST_AST_VERBATIM'), NULL, NULL, '', 'Lors des épreuves orales, vous présenterez 2 épreuves obligatoires de 20 minutes (Entretien Individuel et Anglais) et pourrez présenter une épreuve facultative de langue vivante si vous en avez fait la demande lors de votre inscription au concours.', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ORAL_TEST_BCE_VERBATIM'), NULL, NULL, '', 'Lors des épreuves orales, vous présenterez 3 épreuves obligatoires de 20 minutes (Entretien Individuel, LV1 et LV2)', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ORAL_TEST_AST_COMPLEMENTARY_INFO'), (SELECT id FROM media WHERE original_name = 'icon_info2.svg'), NULL, '', 'Les épreuves se déroulent entre 8h et 18h30. Vos horaires de passage des épreuves vous seront communiquées le jour-même et peuvent évoluer au cours de la journée. Prévoyez donc d’être disponible sur la journée complète et de rester sur le campus choisi.', NULL, NULL, '1', '1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'ORAL_TEST_BCE_COMPLEMENTARY_INFO'), (SELECT id FROM media WHERE original_name = 'icon_info3.svg'), NULL, '', 'Les épreuves se déroulent entre 8h et 18h30. Vos horaires de passage des épreuves vous seront communiquées le jour-même et peuvent évoluer au cours de la journée. Prévoyez donc d’être disponible sur la journée complète et de rester sur le campus choisi.', NULL, NULL, '1', '1', now(), now())");

        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ((SELECT b.id FROM bloc b INNER JOIN bloc_tag bt ON b.tag_id = bt.id WHERE bt.label = 'ORAL_TEST_AST_VERBATIM'), 1)");
        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ((SELECT b.id FROM bloc b INNER JOIN bloc_tag bt ON b.tag_id = bt.id WHERE bt.label = 'ORAL_TEST_AST_VERBATIM'), 2)");
        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ((SELECT b.id FROM bloc b INNER JOIN bloc_tag bt ON b.tag_id = bt.id WHERE bt.label = 'ORAL_TEST_BCE_VERBATIM'), 3)");
        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ((SELECT b.id FROM bloc b INNER JOIN bloc_tag bt ON b.tag_id = bt.id WHERE bt.label = 'ORAL_TEST_AST_COMPLEMENTARY_INFO'), 1)");
        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ((SELECT b.id FROM bloc b INNER JOIN bloc_tag bt ON b.tag_id = bt.id WHERE bt.label = 'ORAL_TEST_AST_COMPLEMENTARY_INFO'), 2)");
        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ((SELECT b.id FROM bloc b INNER JOIN bloc_tag bt ON b.tag_id = bt.id WHERE bt.label = 'ORAL_TEST_BCE_COMPLEMENTARY_INFO'), 3)");
    }

    public function down(Schema $schema): void
    {

    }
}
