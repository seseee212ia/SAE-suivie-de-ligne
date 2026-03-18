<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260318150800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, sae_id INT DEFAULT NULL, enseignant_id INT NOT NULL, INDEX IDX_A2B07288DDE9B22 (sae_id), INDEX IDX_A2B07288E455FCC0 (enseignant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE rendu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date DATE NOT NULL, sae_id INT NOT NULL, etudiant_id INT DEFAULT NULL, groupe_id INT DEFAULT NULL, INDEX IDX_2A7F8EB9DDE9B22 (sae_id), INDEX IDX_2A7F8EB9DDEAB1A3 (etudiant_id), INDEX IDX_2A7F8EB97A45358C (groupe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sae (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(6) NOT NULL, nom LONGTEXT NOT NULL, description LONGTEXT NOT NULL, semestre INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288DDE9B22 FOREIGN KEY (sae_id) REFERENCES sae (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES enseignants (id)');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB9DDE9B22 FOREIGN KEY (sae_id) REFERENCES sae (id)');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB9DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id)');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB97A45358C FOREIGN KEY (groupe_id) REFERENCES etudiants (id)');
        $this->addSql('ALTER TABLE etudiants CHANGE pr?enom prenom VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288DDE9B22');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288E455FCC0');
        $this->addSql('ALTER TABLE rendu DROP FOREIGN KEY FK_2A7F8EB9DDE9B22');
        $this->addSql('ALTER TABLE rendu DROP FOREIGN KEY FK_2A7F8EB9DDEAB1A3');
        $this->addSql('ALTER TABLE rendu DROP FOREIGN KEY FK_2A7F8EB97A45358C');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE rendu');
        $this->addSql('DROP TABLE sae');
        $this->addSql('ALTER TABLE etudiants CHANGE prenom pr?enom VARCHAR(30) NOT NULL');
    }
}
