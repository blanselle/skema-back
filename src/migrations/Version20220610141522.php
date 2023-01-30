<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220610141522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[DATA] fix media fixture cms';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE media SET
            file = 'public/fixtures/documents/PresentationConcoursAST2022.pdf'
            WHERE id = 24;"
        );

        $this->addSql("UPDATE media SET 
            file = 'public/fixtures/documents/Reglement_AST.pdf'
            WHERE id = 25;"
        );

        
    }

    public function down(Schema $schema): void
    {
    }
}




