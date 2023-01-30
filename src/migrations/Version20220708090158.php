<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708090158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('delete from cv_language');
        $this->addSql('delete from language');

        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'aa','Afar')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ab','Abkhaze')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'af','Afrikaans')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ak','Akan')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sq','Albanais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'am','Amharique')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ar','Arabe')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'an','Aragonais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'hy','Arménien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'as','Assamais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'av','Avar')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ae','Avestique')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ay','Aymara')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'az','Azéri')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ba','Bachkir')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bm','Bambara')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'eu','Basque')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'be','Biélorusse')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bn','Bengali')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bb','Berbères')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bh','Langues Biharis')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bi','Bichlamar')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bs','Bosniaque')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'br','Breton')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bg','Bulgare')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'my','Birman')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ca','Catalan; Valencien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ch','Chamorro')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ce','Tchétchène')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'zh','Chinois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'cu','Slavon D''église; Vieux Slave; Slavon Liturgique; Vieux Bulgare')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'cv','Tchouvache')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kw','Cornique')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'co','Corse')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'cr','Cree')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'cs','Tchèque')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'da','Danois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'dv','Maldivien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'nl','Néerlandais; Flamand')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'dz','Dzongkha')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'en','Anglais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'eo','Espéranto')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'et','Estonien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ee','Éwé')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'fo','Féroïen')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'fj','Fidjien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'fi','Finnois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'fr','Français')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'fy','Frison Occidental')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ff','Peul')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ka','Géorgien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'de','Allemand')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'gd','Gaélique; Gaélique Écossais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ga','Irlandais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'gl','Galicien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'gv','Manx; Mannois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'el','Grec Moderne (après 1453)')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'gn','Guarani')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'gu','Goudjrati')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ht','Haïtien; Créole Haïtien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ha','Haoussa')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'he','Hébreu')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'hz','Herero')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'hi','Hindi')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ho','Hiri Motu')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'hr','Croate')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'hu','Hongrois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ig','Igbo')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'is','Islandais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'io','Ido')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ii','Yi De Sichuan')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'iu','Inuktitut')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ie','Interlingue')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ia','Interlingua (langue Auxiliaire Internationale)')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'id','Indonésien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ik','Inupiaq')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'it','Italien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'jv','Javanais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ja','Japonais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kl','Groenlandais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kn','Kannada')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ks','Kashmiri')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kr','Kanouri')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kk','Kazakh')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'km','Khmer Central')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ki','Kikuyu')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'rw','Rwanda')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ky','Kirghiz')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kv','Kom')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kg','Kongo')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ko','Coréen')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'kj','Kuanyama; Kwanyama')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ku','Kurde')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'lo','Lao')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'la','Latin')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'lv','Letton')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'li','Limbourgeois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ln','Lingala')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'lt','Lituanien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'lb','Luxembourgeois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'lu','Luba-katanga')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'lg','Ganda')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'mk','Macédonien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'mh','Marshall')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ml','Malayalam')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'mi','Maori')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'mr','Marathe')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ms','Malais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'mg','Malgache')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'mt','Maltais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'mn','Mongol')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'na','Nauruan')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'nv','Navaho')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'nr','Ndébélé Du Sud')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'nd','Ndébélé Du Nord')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ng','Ndonga')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ne','Népalais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'nn','Norvégien Nynorsk; Nynorsk, Norvégien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'nb','Norvégien Bokmål')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'no','Norvégien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ny','Chichewa; Chewa; Nyanja')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'oc','Occitan (après 1500); Provençal')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'oj','Ojibwa')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'or','Oriya')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'om','Galla')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'os','Ossète')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'pa','Pendjabi')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'fa','Persan')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'pi','Pali')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'pl','Polonais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'pt','Portugais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ps','Pachto')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'qu','Quechua')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'rm','Romanche')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ro','Roumain; Moldave')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'rn','Rundi')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ru','Russe')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sg','Sango')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sa','Sanskrit')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'si','Singhalais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sk','Slovaque')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sl','Slovène')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'se','Sami Du Nord')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sm','Samoan')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sn','Shona')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sd','Sindhi')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'so','Somali')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'st','Sotho Du Sud')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'es','Espagnol; Castillan')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sc','Sarde')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sr','Serbe')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ss','Swati')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'su','Soundanais')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sw','Swahili')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'sv','Suédois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ty','Tahitien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ta','Tamoul')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'tt','Tatar')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'te','Télougou')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'tg','Tadjik')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'tl','Tagalog')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'th','Thaï')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'bo','Tibétain')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ti','Tigrigna')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'to','Tongan (Îles Tonga)')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'tn','Tswana')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ts','Tsonga')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'tk','Turkmène')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'tr','Turc')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'tw','Twi')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ug','Ouïgour')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'uk','Ukrainien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ur','Ourdou')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'uz','Ouszbek')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'ve','Venda')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'vi','Vietnamien')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'vo','Volapük')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'cy','Gallois')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'wa','Wallon')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'wo','Wolof')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'xh','Xhosa')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'yi','Yiddish')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'yo','Yoruba')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'za','Zhuang; Chuang')");
        $this->addSql("insert into language (id, code, label) VALUES (nextval('language_id_seq'),'zu','Zoulou')");
    }

    public function down(Schema $schema): void
    {
    }
}
