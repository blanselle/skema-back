<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221115082817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add programChannels to bloc';
    }

    public function up(Schema $schema): void
    {
        $stmt = $this->connection->executeQuery("SELECT id FROM program_channel");
        $programChannels = $stmt->fetchAllAssociative();
        $stmt = $this->connection->executeQuery("SELECT id FROM bloc as b WHERE b.key='ERROR_CANDIDACY_SUBMISSION_POPIN'");
        $bloc = $stmt->fetchAssociative();

        if (false !== $bloc) {
            foreach($programChannels as $programChannel) {
                $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ({$bloc['id']}, {$programChannel['id']})");
            }
        }
    }

    public function down(Schema $schema): void {}
}
