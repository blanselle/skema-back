<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221027093244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New blocs fixtures';
    }

    public function up(Schema $schema): void
    {
        $blocs = [
            ['tag' => 'WRITTEN_TEST_MANAGEMENT_NOTA_BENE_FNEGE', 'content' => '<p><strong>NB :</strong> SKEMA récupère automatiquement votre score auprès de la FNEGE</p>'],
            ['tag' => 'WRITTEN_TEST_ENGLISH_SCORE_SUBTITLE', 'content' => '<p>Vous avez déjà un score de test de langue et/ou êtes inscrits à une future session</p>'],
            ['tag' => 'WRITTEN_TEST_ENGLISH_SCORE_NOTA_BENE', 'content' => '<p>Lorem ipsum dolor sit amet</p>'],
            ['tag' => 'WRITTEN_TEST_ENGLISH_INSCRIPTION_SUBTITLE', 'content' => '<p>Vous souhaitez vous inscrire à une session TOEIC® ou iCIMS® SKEMA ?</p>'],
            ['tag' => 'WRITTEN_TEST_ENGLISH_INSCRIPTION_NOTA_BENE', 'content' => '<p>Vous avez déjà un score de test de management et/ou êtes inscrits à une future session</p>'],
            ['tag' => 'WRITTEN_TEST_MANAGEMENT_SCORE_NOTA_BENE', 'content' => "<p><strong>NB :</strong> Les tests acceptés sont le TAGE2® dans le cadre du concours AST1, et le TAGE MAGE® dans le cadre du concours AST2. Le test GMAT® est également accepté pour ces deux voies de concours. SKEMA Business School retiendra le meilleur score obtenu lors de toutes les sessions présentées entre le 1er janvier de l''année civile précédant l''année du concours et la date limite indiquée dans le calendrier du concours (cf. Annexe 12.1)</p>"],
            ['tag' => 'WRITTEN_TEST_MANAGEMENT_INSCRIPTION_SUBTITLE', 'content' => '<p>Vous souhaitez vous inscrire à une session TAGE® SKEMA ?</p>'],
            ['tag' => 'WRITTEN_TEST_MANAGEMENT_INSCRIPTION_NOTA_BENE', 'content' => '<p>Lorem ipsum dolor sit amet</p>'],
        ];

        foreach ($blocs as $bloc) {
            $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'), '{$bloc['tag']}', NOW(), NOW())");

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
                (SELECT id FROM bloc_tag WHERE label = '{$bloc['tag']}'),
                null,
                null,
                '{$bloc['content']}',
                '0',
                '1',
                NOW(),
                NOW()
            )");
        }

        $stmt = $this->connection->executeQuery("SELECT id FROM program_channel");
        $programChannels = $stmt->fetchAllAssociative();
        $stmt = $this->connection->executeQuery("SELECT id FROM bloc LEFT JOIN bloc_program_channel bpc on bloc.id = bpc.bloc_id WHERE bpc.bloc_id IS NULL ORDER BY id ASC");
        $blocs = $stmt->fetchAllAssociative();

        foreach ($blocs as $entity) {
            foreach($programChannels as $programChannel) {
                $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ({$entity['id']}, {$programChannel['id']})");
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}
