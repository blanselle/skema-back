<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221001192251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] Add payment bloc';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO bloc_tag (
            id, 
            label, 
            created_at, 
            updated_at
        ) VALUES (
            nextval('bloc_tag_id_seq'),
            'PAYMENT',
            NOW(),
            NOW()
        )");

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
            (SELECT id FROM bloc_tag WHERE label = 'PAYMENT'),
            'MSG_PAYMENT_IN_PROGRESS',
            null,
            '<p>Votre paiement est en cours de traitement</p><p>vous recevrez une notification et un email lorsque celui-ci sera traité</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

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
            (SELECT id FROM bloc_tag WHERE label = 'PAYMENT'),
            'NOTIFICATION_PAYMENT_VALIDATED',
            null,
            '<p>Confirmation de paiement</p><p>Vos frais \"%label%\" ont bien été réglés.</p><p>%content%</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

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
            (SELECT id FROM bloc_tag WHERE label = 'PAYMENT'),
            'NOTIFICATION_PAYMENT_REJECTED',
            null,
            '<p>Paiement refusé</p><p>Le paiement de vos frais \"%label%\" n’a pas abouti.</p><p>Si vous ne pouvez pas payer en ligne, contactez-nous via le formulaire de contact. </p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");

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
            (SELECT id FROM bloc_tag WHERE label = 'PAYMENT'),
            'NOTIFICATION_PAYMENT_CANCELED',
            null,
            '<p>Paiement annulé</p><p>Le paiement de vos frais \"%label%\" a été annulé.</p>',
            '0',
            '1',
            NOW(),
            NOW()
        )");
    }

    public function down(Schema $schema): void
    {

    }
}
