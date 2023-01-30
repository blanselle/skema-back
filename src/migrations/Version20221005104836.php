<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221005104836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Add program channel on blocs and parameters';
    }

    public function up(Schema $schema): void
    {
        $stmt = $this->connection->executeQuery("SELECT id FROM program_channel");
        $programChannels = $stmt->fetchAllAssociative();
        $stmt = $this->connection->executeQuery("SELECT id FROM bloc LEFT JOIN bloc_program_channel bpc on bloc.id = bpc.bloc_id WHERE bpc.bloc_id IS NULL ORDER BY id ASC");
        $blocs = $stmt->fetchAllAssociative();

        foreach ($blocs as $entity) {
            foreach($programChannels as $programChannel) {
                $this->addSql("INSERT INTO bloc_program_channel (bloc_id, program_channel_id) VALUES ({$entity['id']}, {$programChannel['id']})");
            }
        }

        $stmt = $this->connection->executeQuery("SELECT id FROM parameters LEFT JOIN parameter_program_channel ppc on parameters.id = ppc.parameter_id WHERE ppc.parameter_id IS NULL ORDER BY id ASC");
        $parameters = $stmt->fetchAllAssociative();
        foreach ($parameters as $entity) {
            foreach($programChannels as $programChannel) {
                $this->addSql("INSERT INTO parameter_program_channel (parameter_id, program_channel_id) VALUES ({$entity['id']}, {$programChannel['id']})");
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}
