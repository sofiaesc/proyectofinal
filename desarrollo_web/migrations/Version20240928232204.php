<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928232204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test ADD fecha_hora DATETIME NOT NULL, ADD foto LONGBLOB NOT NULL, DROP fecha, DROP hora, DROP informe');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE test ADD fecha DATE NOT NULL, ADD hora TIME NOT NULL, ADD informe LONGBLOB DEFAULT NULL, DROP fecha_hora, DROP foto');
    }
}
