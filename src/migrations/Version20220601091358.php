<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220601091358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'NotificationTemplate entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE notification_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE notification_template (id INT NOT NULL, subject VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE notification ADD notification_template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD0413CF9 FOREIGN KEY (notification_template_id) REFERENCES notification_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BF5476CAD0413CF9 ON notification (notification_template_id)');

        $templates = [
            ['id' => 1, 'subject' => "Document non lisible", 'content' => "Le document doit être lisible"],
            ['id' => 2, 'subject' => "Document non officiel", 'content' => "Le document doit être sur papier à en-tête et porter le tampon de l'établissement ou de l'organisme"],
            ['id' => 3, 'subject' => "Date antérieure à l'année scolaire", 'content' => "Le document doit être sur papier à en-tête et porter le tampon de l'établissement ou de l'organisme"],
            ['id' => 4, 'subject' => "Date incohérente", 'content' => "Le document ne correspond pas à la date indiquée dans le formulaire"],
            ['id' => 5, 'subject' => "Document incomplet", 'content' => "Le document est incomplet (recto et verso)"]
        ];
        foreach ($templates as $template) {
            $this->addSql('INSERT INTO notification_template (id, subject, content) VALUES (:id, :subject, :content)', $template);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAD0413CF9');
        $this->addSql('DROP SEQUENCE notification_template_id_seq CASCADE');
        $this->addSql('DROP TABLE notification_template');
        $this->addSql('DROP INDEX IDX_BF5476CAD0413CF9');
        $this->addSql('ALTER TABLE notification DROP notification_template_id');
    }
}
