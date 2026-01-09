<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109135516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE factory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE machine (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(25) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE machine_factory (machine_id INT NOT NULL, factory_id INT NOT NULL, INDEX IDX_FD498E49F6B75B26 (machine_id), INDEX IDX_FD498E49C7AF27D2 (factory_id), PRIMARY KEY (machine_id, factory_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE machine_factory ADD CONSTRAINT FK_FD498E49F6B75B26 FOREIGN KEY (machine_id) REFERENCES machine (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE machine_factory ADD CONSTRAINT FK_FD498E49C7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE machine_factory DROP FOREIGN KEY FK_FD498E49F6B75B26');
        $this->addSql('ALTER TABLE machine_factory DROP FOREIGN KEY FK_FD498E49C7AF27D2');
        $this->addSql('DROP TABLE factory');
        $this->addSql('DROP TABLE machine');
        $this->addSql('DROP TABLE machine_factory');
    }
}
