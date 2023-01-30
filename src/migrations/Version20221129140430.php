<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221129140430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Admissibility] Add blocs';
    }

    public function up(Schema $schema): void
    {
        $tags = [
            ['label' => 'DASHBOARD_ORAUX_RECAP_TITRE'],
            ['label' => 'DASHBOARD_ORAUX_RECAP_EPREUVE'],
            ['label' => 'DASHBOARD_ORAUX_PREPA'],
        ];
        foreach ($tags as $tag) {
            $this->addTag($tag['label']);
        }

        $blocs = [
            ['tag' => 'ADMISSIBILITY_RESULT', 'key' => 'ADMISSIBILITY_RESULT_OK', 'label' => NULL, 'content' => '<p>Bravo !</p><p>Vous êtes admissible aux épreuves Orales du Concours</p><p>Vous avez <strong>jusqu\'au %parameter.dateFermetureRDV% pour choisir une date et un Campus </strong>pour passer vos épreuves orales (en fonction des places disponibles)</p>'],
            ['tag' => 'ADMISSIBILITY_RESULT', 'key' => 'ADMISSIBILITY_RESULT_KO', 'label' => NULL, 'content' => 'Votre candidature n’est pas admissible.'],
            ['tag' => 'DASHBOARD_ORAUX_RECAP_TITRE', 'key' => NULL, 'label' => NULL, 'content' => 'Epreuves orales'],
            ['tag' => 'DASHBOARD_ORAUX_RECAP_EPREUVE', 'key' => NULL, 'label' => NULL, 'content' => '<ul><li><strong>L\'entretien</strong></br>Il s\'agit d\'un entretien de motivation au cours duquel vous présenterez le CV qui pourrait être le vôtre dans 10 ans. </br><a href="https://concours.skema-bs.fr/" title="En savoir plus">En savoir plus</a></li></ul>', 'position' => 0],
            ['tag' => 'DASHBOARD_ORAUX_RECAP_EPREUVE', 'key' => NULL, 'label' => NULL, 'content' => '<ul><li><strong>Les langues</strong></br>Vous présenterez une synthèse d\'un article de presse en Anglais qui vous sera attribué lors de l\'épreuve. </br><a href="https://concours.skema-bs.fr/" title="En savoir plus">En savoir plus</a></li></ul>', 'position' => 1],
            ['tag' => 'DASHBOARD_ORAUX_PREPA', 'key' => NULL, 'label' => NULL, 'content' => '<strong>Entraîne-toi aux oraux!</strong> </br><a href="https://concours.skema-bs.fr/" title="Consulte nos modules de préparation">Consulte nos modules de préparation</a>'],
        ];
        foreach ($blocs as $bloc) {
            $this->addBloc($bloc);
        }
    }

    public function down(Schema $schema): void
    {
    }

    private function addBloc(array $bloc): void
    {
        $tagLabel = $bloc['tag'];
        $key = $bloc['key'];
        $label = $bloc['label'];
        $content = $bloc['content'];
        $position = $bloc['position']?? 0;

        $this->addSql("INSERT INTO bloc (
            id,
            tag_id,
            key,
            label,
            content,
            position,
            active,
            created_at,
            updated_at
        ) VALUES (
            nextval('bloc_id_seq'),
            (SELECT id FROM bloc_tag WHERE label = :tag),
            :key,
            :label,
            :content,
            :position,
            '1',
            NOW(),
            NOW()
        )", [
            'tag' => $tagLabel,
            'key' => $key,
            'label' => $label,
            'content' => $content,
            'position' => $position,
        ]);


        $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id)
            SELECT  (SELECT MAX(id) FROM bloc), pc.id
            FROM (SELECT id FROM program_channel) pc
        ");
    }

    private function addTag(string $label): void
    {
        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), '$label', NOW(), NOW())");
    }
}
