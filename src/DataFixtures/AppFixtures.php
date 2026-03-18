<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etu = new Etudiants();
        $etu->setNumEtudiant("12345678");
        $etu->setRoles("ROLE_ETUDIANT");
        $etu->setPassword("mdpetudiant");
        $etu->setNom("NomEtu1");
        $etu->setPrenom("PrenomEtu1");        
        $etu->setPromotion("2");
        $etu->setSpecialite("DW");

        $ens = new Enseignants();
        $ens->setUser("Ens1");
        $ens->setPassword();
        $ens->setRoles("ROLE_ENSEIGNANT");
        $ens->setPassword("mdpenseignant");
        $ens->setNom("NomEns1");
        $ens->setPrenom("PrenomEns1");

        $admin = new Admin();
        $admin->setUser("Admin");
        $admin->setRoles("ROLE_ADMIN");
        $admin->setPassword("mdpadmin");

        $manager->persist($etu);
        $manager->persist($ens);
        $manager->persist($admin);

        $manager->flush();
    }
}
