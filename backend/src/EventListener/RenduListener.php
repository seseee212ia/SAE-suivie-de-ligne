<?php

namespace App\EventListener;

use App\Entity\Rendu;
use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Rendu::class)]
class RenduListener
{
    public function postPersist(Rendu $rendu, LifecycleEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        
        $notification = new Notification();
        
        $nomEtudiant = $rendu->getEtudiant() ? $rendu->getEtudiant()->getNom() : 'Un étudiant inconnu';
        $nomRendu = $rendu->getNom() ?? 'Fichier';
        
        $notification->setMessage("Nouveau rendu déposé par {$nomEtudiant} pour le fichier : {$nomRendu}");
        
        // Optionnel : lier la notification à l'enseignant responsable si la relation existe dans SAE
        // Par exemple si la SAE a un responsable :
        // if ($rendu->getSae() && $rendu->getSae()->getResponsable()) {
        //     $notification->setEnseignant($rendu->getSae()->getResponsable());
        // }

        $entityManager->persist($notification);
        $entityManager->flush();
    }
}
