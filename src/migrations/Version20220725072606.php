<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220725072606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New fields for campus and exam_classification';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE campus ADD instruction TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam_classification ADD equipment TEXT DEFAULT NULL');

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'SUMMONS_ZONE_1', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'SUMMONS_ZONE_1'), NULL, 'SUMMONS_ZONE_1', NULL, '<h1><u>CONVOCATION</u></h1><br /><h2>CONCOURS D’ADMISSION SUR TITRES SKEMA</h2><br /><p style=text-align:left;>Nous vous invitons à vous présenter à <strong>l’épreuve écrite d’admissibilité</strong> :<br /><h3>{%typologie%} le {%exam_date_start%}</h3></p>', NULL, NULL, '1', '1', now(), now())");

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'SUMMONS_ZONE_2', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'SUMMONS_ZONE_2'), NULL, 'SUMMONS_ZONE_2', NULL, '<p>Nous vous demandons de vous munir de cette convocation et OBLIGATOIREMENT d’une pièce officielle d’identité. Pièces d’identité principales* recevables : Avec photographie reconnaissable et signature.<br /><br /><u>POUR LES FRANÇAIS :</u><ul><li>Carte d’identité nationale valide<br />(Attention : Pas de prolongation de validité de 5 ans si vous étiez mineur au moment de sa délivrance)</li><li>ou Permis de conduire national</li><li>ou Passeport</li></ul><u>POUR LES EUROPEENS :</u><ul><li>Carte d’identité de leur pays</li><li>ou Passeport</li></ul><br /><span style=color:red;>Si vous n’avez pas de pièce d’identité autorisée vous ne pouvez pas passer l’examen. Des photocopies depièce d’identité ne seront pas acceptées.</span><br /><br />La présentation de cette convocation ne vous dispense pas de faire parvenir les documents éventuellement manquants à votre dossier d’inscription.<br /><br />Nous vous rappelons que tout candidat en retard ne sera pas accepté dans la salle d’examen après l’ouverture de l’enveloppe contenant le test.<br /><strong>Les candidats présents à l’ouverture du test ne seront pas autorisés à quitter la salle avant la fin de l’épreuve.</strong></p>', NULL, NULL, '1', '1', now(), now())");

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'SUMMONS_ZONE_EQUIPMENT', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'SUMMONS_ZONE_EQUIPMENT'), NULL, 'SUMMONS_ZONE_EQUIPMENT', NULL, 'Vous devrez OBLIGATOIREMENT être muni du matériel cité ci-dessous avant l’accès aux salles d’examen :', NULL, NULL, '1', '1', now(), now())");

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), 'SUMMONS_ZONE_3', now(), now())");
        $this->addSql("INSERT INTO bloc (id, tag_id, media_id, key, label, content, link, label_link, position, active, created_at, updated_at) VALUES (nextval('bloc_id_seq'), (SELECT id FROM bloc_tag WHERE label = 'SUMMONS_ZONE_3'), NULL, 'SUMMONS_ZONE_3', NULL, '<p><strong>ATTENTION:</strong><ul><li>les surligneurs ne sont pas autorisés pour l’ensemble des tests.</li><li>l’utilisation de la machine à calculer est interdite ainsi que tout matériel électronique.</li><li>tout documents écrits, tels dictionnaires ou autre, ne sont pas autorisés.</li><li>Toutes boissons et nourriture sont interdites dans la salle.</li></ul></p><p style=text-align:right;>Le Service Concours<br />SKEMA Business School</p><p>*Toute autre pièce ne sera pas acceptée, de même que les photocopies de pièce d’identité ou d’attestation de perte/vol délivrées par l’administration française.</p><p style=background:black;color:white;text-align:center;padding-top:4px;padding-bottom:4px;><strong>Comportement pendant les épreuves</strong></p><p>Toute infraction au règlement, toute fraude ou tentative de fraude peut entraîner des sanctions pouvant aller jusqu’à l’exclusion définitive des concours, sans préjuger des poursuites qui pourraient être engagées et sans possibilité de se représenter à une autre session du concours ou de tout autre mode d’admission à SKEMA Business School.</p><p style=background:black;color:white;padding-top:4px;padding-bottom:4px;padding-left:10px;><strong>Lors des épreuves</strong></p><p>- conformément aux dispositions du règlement intérieur de SKEMA Business School, il est demandé aux candidats des concours d’avoir une tenue vestimentaire et un comportement corrects à l’intérieur des locaux.<br /><br />Le port de signes ou tenues par lesquels les candidats des concours manifestent ostensiblement une appartenance religieuse est interdit.</p><p style=background:black;color:white;padding-top:4px;padding-bottom:4px;padding-left:10px;padding-right:10px;display:block;><strong>Il est interdit :</strong></p><ul><li>d’avoir un comportement qui pourrait gêner les autres candidats.</li><li>de porter un appareil auditif (à l’exception des candidats malentendants qui auront signalé leur handicap lors de l’inscription).</li><li>de porter un couvre-chef, quel qu’en soit la nature (casquette, chapeau, foulard…). Le personnel de surveillance doit pouvoir prévenir les risques de fraudes en vérifiant l’identité des personnes et en s’assurant qu’aucun dispositif de communication ne soit dissimulé.</li><li>de communiquer avec un autre candidat</li><li>d’utiliser un téléphone portable, celui-ci devra être éteint et rangé dans le sac (il ne servira ni de chronomètre, ni de montre)</li><li>de sortir de la salle d’examen pendant toute la durée de l’épreuve même si le candidat termine avant les autres.</li><li>l’usage des calculatrices, de dictionnaires, d’appareils électroniques, de casques anti-bruit est strictement interdit.</li></ul><p style=background:#ffbaba;padding-top:4px;padding-bottom:4px;padding-left:10px;padding-right:10px;text-align:center;><strong>SKEMA se réserve le droit d’exclure un candidat qui ne respecterait pas ces consignes.</strong></p><p><em>Extrait du règlement des concours AST de SKEMA</em></p><p style=text-align:right;>Le Service Concours<br>SKEMA Business School</p>', NULL, NULL, '1', '1', now(), now())");

        $this->addSql("UPDATE campus set instruction = '<br /><h2 style=text-align:center;>SKEMA Business School – Campus de Sophia Antipolis</h2><p style=text-align:center;>60 Rue Dostoïevski – BP 085 – 06902 SOPHIA ANTIPOLIS CEDEX – Tél : 04.93.95.44.35</p><br /><p><strong>En provenance de Cannes</strong>, sur l’Autoroute A8 prendre la sortie 44 « Antibes/Sophia Antipolis ». Après le péage prendre à droite « direction Sophia Antipolis » (D35).<br /><br /><strong>En provenance de Nice</strong>, sur l’Autoroute A8 prendre la sortie 44 « Antibes/Sophia Antipolis ». Après le péage prendre à gauche « direction Antibes » (D535). Après un grand virage prendre à droite direction « Grasse-Valbonne Sophia Antipolis » (route de grasse).<br /><br />Continuez tout droit (environ 1.4 km), vous arrivez sur la route du Parc.<br />Continuez tout droit puis prendre sur votre droite « accès 3 les lucioles ». Vous êtes dans la rue Albert Einstein.<br /><br />Prendre ensuite la première à droite (rue Ludwig Van Beethoven) et encore la première à droite (rue Dostoïevski). L’entrée de SKEMA se situe au n° 60 sur votre droite (mitoyen du Novotel).</p>' WHERE postal_code = '06902'");

        $this->addSql("INSERT INTO bloc_tag (
            id, 
            label, 
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_tag_id_seq'),
            'NOTIFICATION_SUMMONS_GENERATED',
            NOW(),
            NOW()
        )");
        $this->addSql("INSERT INTO bloc (
            id, 
            tag_id, 
            label, 
            content, 
            link, 
            label_link, 
            position, 
            active, 
            created_at, 
            updated_at, 
            key, 
            media_id
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = 'NOTIFICATION_SUMMONS_GENERATED'),	
            'Votre convocation',	
            'Bonjour %firstname%, votre convocation pour l’épreuve %nom_typologie% est téléchargeable dans la rubrique « Mes épreuves écrites ». Le service concours',
            NULL,
            NULL,
            1,
            '1',
            NOW(),
            NOW(),
            'NOTIFICATION_SUMMONS_GENERATED',
            NULL
        )");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE campus DROP instruction');
        $this->addSql('ALTER TABLE exam_classification DROP equipment');
    }
}
