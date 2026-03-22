<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RenduRepository;
use App\Repository\SaeRepository;
use App\Entity\Sae;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RenduRepository::class)]
#[ApiResource]
class Rendu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    private ?Sae $sae = null;

    #[ORM\ManyToOne]
    private ?Etudiants $etudiant = null;

    #[ORM\ManyToOne]
    private ?Etudiants $groupe = null;

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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

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

    public function getEtudiant(): ?Etudiants
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiants $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getGroupe(): ?Etudiants
    {
        return $this->groupe;
    }

    public function setGroupe(?Etudiants $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
    }
}
