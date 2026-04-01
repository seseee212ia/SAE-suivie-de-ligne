<?php

namespace App\Notifications;

use App\Notifications\DocNotification;
use App\Entity\Documents;
use App\Entity\Sae;
use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Documents::class)]
class DocNotification
{
    public function postPersist(Documents $doc, LifecycleEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        
        $notification = new Notification();
        
        $nomEnseignant = $doc->getEnseignant() ? $doc->getEnseignant()->getNom() : 'Enseignant inconnu';
        $nomDoc = $doc->getNom() ?? 'Fichier';
        $titreSae = $doc->getSae() ?? 'SAE ???';
        
        $notification->setMessage("Nouveau document déposé par Monsieur/Madame {$nomEnseignant} pour la {$titreSae}");

        $entityManager->persist($notification);
        $entityManager->flush();
    }
}
