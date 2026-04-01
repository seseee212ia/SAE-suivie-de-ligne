<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RenduRepository;
use App\Repository\SaeRepository;
use App\Entity\Sae;
use App\Entity\Groupe;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\HttpFoundation\File\File; 
use Vich\UploaderBundle\Mapping\Annotation as Vich; 
use ApiPlatform\Metadata\ApiProperty; 
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ORM\Entity(repositoryClass: RenduRepository::class)]
#[ApiFilter(SearchFilter::class, properties: [
    'nom' => 'partial',
    'etudiant.nom' => 'partial',
    'sae.semestre' => 'exact'
])]
#[ApiResource(
    normalizationContext: ['groups' => ['rendu:read']],
    denormalizationContext: ['groups' => ['rendu:write']],
    operations: [ 
        new Get(), 
        new GetCollection(), 
        new Post( 
            security: "is_granted('ROLE_ETUDIANT')"),
        new Put(security: "is_granted('ROLE_ETUDIANT')"),
        new Delete(security: "is_granted('ROLE_ETUDIANT')")
    ] 
)]
 class Rendu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rendu:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotBlank]
    #[Assert\LessThan(propertyPath: 'dateDebut')]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotNull]
    private ?Sae $sae = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rendu:read'])]
    #[Assert\NotNull]
    private ?Etudiants $etudiant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['rendu:read', 'rendu:write'])]
    private ?Groupe $groupe = null;

    #[ORM\Column(nullable: true)] 
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $img = null; 

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

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): static
    {
        $this->groupe = $groupe;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

        return $this;
    } 
}

