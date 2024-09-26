<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910215902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pocillo ADD test_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pocillo ADD CONSTRAINT FK_45926DE81E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
        $this->addSql('CREATE INDEX IDX_45926DE81E5D0459 ON pocillo (test_id)');
        $this->addSql('ALTER TABLE test ADD usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0CDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_D87F7E0CDB38439E ON test (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pocillo DROP FOREIGN KEY FK_45926DE81E5D0459');
        $this->addSql('DROP INDEX IDX_45926DE81E5D0459 ON pocillo');
        $this->addSql('ALTER TABLE pocillo DROP test_id');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0CDB38439E');
        $this->addSql('DROP INDEX IDX_D87F7E0CDB38439E ON test');
        $this->addSql('ALTER TABLE test DROP usuario_id');
    }
}
