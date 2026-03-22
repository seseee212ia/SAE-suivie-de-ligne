<?php

namespace App\DataFixtures;

use App\Entity\Sae;
use App\Entity\Etudiants;
use App\Entity\Enseignants;
use App\Entity\Admin;
use App\Entity\Rendu;
use App\Entity\Documents;
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
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $prof = new Enseignants();
        $prof->setUser('ens_test');
        $prof->setNom('NomEns');
        $prof->setPrenom('PrenomEns');
        $prof->setRoles(['ROLE_ENSEIGNANT']);
        $prof->setPassword($this->passwordHasher->hashPassword($prof, 'password'));
        $manager->persist($prof);

        $etudiant = new Etudiants();
        $etudiant->setNumEtudiant('12345678');
        $etudiant->setNom('NomEtu');
        $etudiant->setPrenom('PrenomEtu');
        $etudiant->setPromotion(2);
        $etudiant->setSpecialite('Développement Web');
        $etudiant->setRoles(['ROLE_ETUDIANT']);
        $etudiant->setPassword($this->passwordHasher->hashPassword($etudiant, 'password'));
        $manager->persist($etudiant);

        $sae = new Sae();
        $sae->setTitre('SAE403');
        $sae->setNom('Architecture logicielle et API');
        $sae->setDescription('Conception de base de données et API.');
        $sae->setSemestre(4);
        $sae->setDateDebut(new \DateTimeImmutable('2024-01-15'));
        $sae->setDateFin(new \DateTime('2024-06-15'));
        $manager->persist($sae);

        $rendu = new Rendu();
        $rendu->setNom('Livrable.pdf');
        $rendu->setDate(new \DateTimeImmutable('2024-05-30'));
        $rendu->setSae($sae);
        $rendu->setEtudiant($etudiant);
        $manager->persist($rendu);

        $manager->flush();
    }
}