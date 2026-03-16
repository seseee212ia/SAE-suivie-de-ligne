<?php

namespace App\Entity;

use App\Repository\EtudiantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantsRepository::class)]
class Etudiants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $num_etudiant = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?int $promo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spécialité = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumEtudiant(): ?int
    {
        return $this->num_etudiant;
    }

    public function setNumEtudiant(int $num_etudiant): static
    {
        $this->num_etudiant = $num_etudiant;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $$mdp;

        return $this;
    }

    public function getPromo(): ?int
    {
        return $this->promo;
    }

    public function setPromo(int $promo): static
    {
        $this->promo = $promo;

        return $this;
    }

    public function getSpécialité(): ?string
    {
        return $this->spécialité;
    }

    public function setSpécialité(?string $spécialité): static
    {
        $this->spécialité = $spécialité;

        return $this;
    }
}
