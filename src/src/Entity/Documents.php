<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
#[ApiResource]
class Documents
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    private ?Sae $sae = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enseignants $enseignant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSae(): ?Sae
    {
        return $this->sae;
    }

    public function setSae(?Sae $sae): static
    {
        $this->sae = $sae;

        return $this;
    }

    public function getEnseignant(): ?Enseignants
    {
        return $this->enseignant;
    }

    public function setEnseignant(?Enseignants $enseignant): static
    {
        $this->enseignant = $enseignant;

        return $this;
    }
}
