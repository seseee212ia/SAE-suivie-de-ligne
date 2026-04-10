<?php

namespace App\DataFixtures;

use App\Entity\Sae;
use App\Entity\Etudiants;
use App\Entity\Enseignants;
use App\Entity\Admin;
use App\Entity\Rendu;
use App\Entity\Documents;
use App\Enum\Semestre;
use App\Enum\Promotion;
use App\Enum\Specialite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setUser('admin_test');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $prof = new Enseignants();
        $prof->setUser('ens_test');
        $prof->setNom('NomEns');
        $prof->setPrenom('PrenomEns');
        $prof->setRoles(['ROLE_ENSEIGNANT']);
        $prof->setPassword($this->passwordHasher->hashPassword($prof, 'ens123'));
        $manager->persist($prof);

        $etudiant = new Etudiants();
        $etudiant->setNumEtudiant('22301234');
        $etudiant->setNom('NomEtu');
        $etudiant->setPrenom('PrenomEtu');
        $etudiant->setPromotion(Promotion::BUT2);
        $etudiant->setSpecialite(Specialite::DEVELOPPEMENT_WEB);
        $etudiant->setRoles(['ROLE_ETUDIANT']);
        $etudiant->setPassword($this->passwordHasher->hashPassword($etudiant, 'etu123'));
        $manager->persist($etudiant);

        $sae = new Sae();
        $sae->setTitre('SAE403');
        $sae->setNom('Architecture logicielle et API');
        $sae->setDescription('Conception de base de données et API.');
        $sae->setSemestre(Semestre::S4);
        $sae->setDateDebut(new \DateTime('2026-01-15'));
        $sae->setDateFin(new \DateTime('2026-06-15'));
        $manager->persist($sae);

        $rendu = new Rendu();
        $rendu->setNom('Livrable');
        $rendu->setDescription('rendu du compte rendu sae 301');
        $rendu->setDate(new \DateTimeImmutable('2026-05-30'));
        $rendu->setSae($sae);
        $rendu->setEtudiant($etudiant);
        $rendu->setImg('Img_couverture.jpg');
        $manager->persist($rendu);

        $document = new Documents();
        $document->setNom('Consigne');
        $document->setSae($sae);
        $document->setEnseignant($prof);
        $document->setFileName('Consigne_sae303.pdf');
        $manager->persist($document);

        $manager->flush();
    }
}