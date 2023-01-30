<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220829125028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Bac Distinction + traits';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE bac_distinction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bac_distinction (id INT NOT NULL, label VARCHAR(100) NOT NULL, code VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE bac ADD bac_distinction_id INT NULL');
        $this->addSql('ALTER TABLE bac ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bac ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bac ADD CONSTRAINT FK_1C4FAC5833974808 FOREIGN KEY (bac_distinction_id) REFERENCES bac_distinction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1C4FAC5833974808 ON bac (bac_distinction_id)');

        $this->addSql('ALTER TABLE bac_channel ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bac_channel ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bac_option ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bac_option ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bac_type ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bac_type ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');

        $this->addSql("UPDATE bac SET created_at = now(), updated_at = now()");
        $this->addSql("UPDATE bac_channel SET created_at = now(), updated_at = now()");
        $this->addSql("UPDATE bac_option SET created_at = now(), updated_at = now()");
        $this->addSql("UPDATE bac_type SET created_at = now(), updated_at = now()");

        $this->addSql("INSERT INTO bac_distinction (id, label, code, created_at, updated_at) VALUES (1, 'Très Bien', 'distinction_tb', now(), now()), (2, 'Bien', 'distinction_b', now(), now()), (3, 'Assez Bien', 'distinction_ab', now(), now()), (4, 'Pas de mention', 'no_distinction', now(), now())");
        $this->addSql("SELECT setval('bac_distinction_id_seq', 5, true)");

        $data = $this->connection->fetchAllAssociative(
            'SELECT * FROM bac',
        );

        foreach ($data as $item) {
            $bac_distinction_id = match ($item['distinction']) {
                'TB' => 1,
                'B' => 2,
                'AB' => 3,
                default => 4
            };
            $this->addSql("UPDATE bac SET bac_distinction_id = {$bac_distinction_id} WHERE id = {$item['id']}");
        }

        $this->addSql('ALTER TABLE bac DROP distinction');
        $this->addSql('ALTER TABLE "bac"
            ALTER "bac_distinction_id" TYPE integer,
            ALTER "bac_distinction_id" DROP DEFAULT,
            ALTER "bac_distinction_id" SET NOT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bac DROP CONSTRAINT FK_1C4FAC5833974808');
        $this->addSql('DROP SEQUENCE bac_distinction_id_seq CASCADE');
        $this->addSql('DROP TABLE bac_distinction');
        $this->addSql('ALTER TABLE bac_channel DROP created_at');
        $this->addSql('ALTER TABLE bac_channel DROP updated_at');
        $this->addSql('DROP INDEX IDX_1C4FAC5833974808');
        $this->addSql('ALTER TABLE bac ADD distinction VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE bac DROP bac_distinction_id');
        $this->addSql('ALTER TABLE bac DROP created_at');
        $this->addSql('ALTER TABLE bac DROP updated_at');
        $this->addSql('ALTER TABLE bac_option DROP created_at');
        $this->addSql('ALTER TABLE bac_option DROP updated_at');
        $this->addSql('ALTER TABLE bac_type DROP created_at');
        $this->addSql('ALTER TABLE bac_type DROP updated_at');
    }
}
