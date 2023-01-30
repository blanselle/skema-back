<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221005104837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Account activationAdd program channel on blocs and parameters';
    }

    public function up(Schema $schema): void
    { 
        $this->addSql("UPDATE bloc SET 
            label = 'Réinitialiser son mot de passe', 
            content = 'Vous avez fait une demande de reinitialisation de mot de passe pour le compte %default.email%. <br />Si vous êtes pas à l''origine de ce mail merci d''ignorer ce message. <br />Pour continuer la démarche de réinitialisation, nous vous invitons à cliquer <a href=''%default.link%''>ici</a>'
            WHERE key = 'MAIL_RESET_PASSWORD'
        ");

        $this->addSql("INSERT INTO bloc_tag (id, label, created_at, updated_at) VALUES (nextval('bloc_tag_id_seq'),'ACCOUNT_ACTIVATION', NOW(), NOW())");

        $this->addBloc(
            key: 'ACCOUNT_ACTIVATION_MAIL',
            label: 'Activer votre compte',
            content: "<p>Bonjour %default.firstname%,<br /> <br />Cliquer <a href='%default.link%'>ici</a> pour activer votre compte sur le site concours Skema et compléter votre dossier de candidature. <br /><br />Le service concours",
        );

        $this->addBloc(
            key: 'ERROR_UNACTIVE_ACCOUNT',
            content: 'Impossible de se connecter, ce compte n\'est pas encore activé', 
        );

        $this->addBloc(
            key: 'ACCOUNT_ACTIVED_MESSAGE',
            content: '<p>Vous allez recevoir un e-mail afin d’activer votre compte. <br />Merci de cliquer sur le lien d’activation afin de pouvoir accéder à votre espace.</p>', 
        );
    
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
            (SELECT id FROM bloc_tag WHERE label = 'ERRORS'),
            :key,
            :label,
            :content,
            '0',
            '1',
            NOW(),
            NOW()
        )", [
            'key' => 'EXPIRED_TOKEN_MESSAGE',
            'label' => '',
            'content' => 'Le token est expiré',
        ]);
    }

    public function down(Schema $schema): void
    {
    }

    private function addBloc(string $key, string $label = '', string $content = ''): void
    {
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
            (SELECT id FROM bloc_tag WHERE label = 'ACCOUNT_ACTIVATION'),
            :key,
            :label,
            :content,
            '0',
            '1',
            NOW(),
            NOW()
        )", [
            'key' => $key,
            'label' => $label,
            'content' => $content,
        ]);
    }
}
