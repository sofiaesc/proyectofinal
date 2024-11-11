<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241110224832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pocillo (id INT AUTO_INCREMENT NOT NULL, test_id INT DEFAULT NULL, fila INT NOT NULL, columna INT NOT NULL, intensidad DOUBLE PRECISION NOT NULL, INDEX IDX_45926DE81E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, usuario_id INT DEFAULT NULL, pocillos_hab VARCHAR(255) NOT NULL, nombre_alt VARCHAR(255) DEFAULT NULL, descripción VARCHAR(5000) DEFAULT NULL, ruta_imagen VARCHAR(255) DEFAULT NULL, fecha_hora DATETIME NOT NULL, INDEX IDX_D87F7E0CDB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pocillo ADD CONSTRAINT FK_45926DE81E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0CDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pocillo DROP FOREIGN KEY FK_45926DE81E5D0459');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0CDB38439E');
        $this->addSql('DROP TABLE pocillo');
        $this->addSql('DROP TABLE test');
        $this->addSql('DROP TABLE usuario');
    }
}
