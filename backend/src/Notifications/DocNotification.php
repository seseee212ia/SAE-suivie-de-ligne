<?php
namespace App\Notifications;

use App\Entity\Documents;
use App\Entity\Sae;
use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Documents::class)]
class DocNotification
{
    public function prePersist(Documents $doc, LifecycleEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();

        $notification = new Notification();

        $nomEnseignant = $doc->getEnseignant() ? $doc->getEnseignant()->getNom() : 'Enseignant inconnu';
        $nomDoc = $doc->getNom() ?? 'Fichier';
        $titreSae = $doc->getSae() ? $doc->getSae()->getTitre() : 'SAE ???';

        $notification->setMessage("Nouveau document déposé par Monsieur/Madame {$nomEnseignant} pour la {$titreSae}");

        $entityManager->persist($notification);
    }
}