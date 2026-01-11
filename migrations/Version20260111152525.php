<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260111152525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prix DOUBLE PRECISION NOT NULL, categorie VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, reservation_date DATETIME NOT NULL, nbr_personne DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, customer_id INT NOT NULL, table_res_id INT NOT NULL, INDEX IDX_42C849559395C3F3 (customer_id), INDEX IDX_42C8495518A2E3C7 (table_res_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE `table` (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, capacite INT NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495518A2E3C7 FOREIGN KEY (table_res_id) REFERENCES `table` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849559395C3F3');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495518A2E3C7');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE `table`');
    }
}
