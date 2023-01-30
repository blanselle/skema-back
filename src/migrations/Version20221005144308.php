<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221005144308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE event_program_channel (event_id INT NOT NULL, program_channel_id INT NOT NULL, PRIMARY KEY(event_id, program_channel_id))');
        $this->addSql('CREATE INDEX IDX_E0C012A371F7E88B ON event_program_channel (event_id)');
        $this->addSql('CREATE INDEX IDX_E0C012A364CF5C1E ON event_program_channel (program_channel_id)');
        $this->addSql('ALTER TABLE event_program_channel ADD CONSTRAINT FK_E0C012A371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_program_channel ADD CONSTRAINT FK_E0C012A364CF5C1E FOREIGN KEY (program_channel_id) REFERENCES program_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $events = $this->connection->fetchAllAssociative("SELECT id FROM event");
        $programChannels = $this->connection->fetchAllAssociative("SELECT id FROM program_channel");
        foreach ($events as $event) {
            foreach($programChannels as $programChannel) {

                $this->addSql("INSERT INTO event_program_channel (event_id, program_channel_id)
                VALUES ({$event['id']}, {$programChannel['id']})");
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event_program_channel DROP CONSTRAINT FK_E0C012A371F7E88B');
        $this->addSql('ALTER TABLE event_program_channel DROP CONSTRAINT FK_E0C012A364CF5C1E');
        $this->addSql('DROP TABLE event_program_channel');
    }
}
