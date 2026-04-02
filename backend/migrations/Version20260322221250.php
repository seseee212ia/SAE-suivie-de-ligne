<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260322221250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, user VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USER (user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, sae_id INT NOT NULL, enseignant_id INT NOT NULL, INDEX IDX_A2B07288DDE9B22 (sae_id), INDEX IDX_A2B07288E455FCC0 (enseignant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE enseignants (id INT AUTO_INCREMENT NOT NULL, user VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USER (user), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE etudiants (id INT AUTO_INCREMENT NOT NULL, num_etudiant INT NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, promotion INT NOT NULL, specialite VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_NUM_ETUDIANT (num_etudiant), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE groupe_etudiants (groupe_id INT NOT NULL, etudiants_id INT NOT NULL, INDEX IDX_72E8D1DC7A45358C (groupe_id), INDEX IDX_72E8D1DCA873A5C6 (etudiants_id), PRIMARY KEY (groupe_id, etudiants_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE rendu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date DATE NOT NULL, sae_id INT NOT NULL, etudiant_id INT NOT NULL, groupe_id INT DEFAULT NULL, INDEX IDX_2A7F8EB9DDE9B22 (sae_id), INDEX IDX_2A7F8EB9DDEAB1A3 (etudiant_id), INDEX IDX_2A7F8EB97A45358C (groupe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sae (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(7) NOT NULL, nom LONGTEXT NOT NULL, description LONGTEXT NOT NULL, semestre INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, UNIQUE INDEX UNIQ_E8C8C02AFF7747B4 (titre), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288DDE9B22 FOREIGN KEY (sae_id) REFERENCES sae (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES enseignants (id)');
        $this->addSql('ALTER TABLE groupe_etudiants ADD CONSTRAINT FK_72E8D1DC7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_etudiants ADD CONSTRAINT FK_72E8D1DCA873A5C6 FOREIGN KEY (etudiants_id) REFERENCES etudiants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB9DDE9B22 FOREIGN KEY (sae_id) REFERENCES sae (id)');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB9DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id)');
        $this->addSql('ALTER TABLE rendu ADD CONSTRAINT FK_2A7F8EB97A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288DDE9B22');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288E455FCC0');
        $this->addSql('ALTER TABLE groupe_etudiants DROP FOREIGN KEY FK_72E8D1DC7A45358C');
        $this->addSql('ALTER TABLE groupe_etudiants DROP FOREIGN KEY FK_72E8D1DCA873A5C6');
        $this->addSql('ALTER TABLE rendu DROP FOREIGN KEY FK_2A7F8EB9DDE9B22');
        $this->addSql('ALTER TABLE rendu DROP FOREIGN KEY FK_2A7F8EB9DDEAB1A3');
        $this->addSql('ALTER TABLE rendu DROP FOREIGN KEY FK_2A7F8EB97A45358C');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE enseignants');
        $this->addSql('DROP TABLE etudiants');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE groupe_etudiants');
        $this->addSql('DROP TABLE rendu');
        $this->addSql('DROP TABLE sae');
    }
}
