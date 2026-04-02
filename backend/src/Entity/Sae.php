<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\Semestre;
use App\Repository\SaeRepository;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['sae:read']],
    denormalizationContext: ['groups' => ['sae:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['titre' => 'partial', 'nom' => 'partial'])]
class Sae
{
    public function __construct()
    {
        $this->rendus = new ArrayCollection();
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sae:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 7, unique: true)]
    #[Groups(['sae:read', 'sae:write'])]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(min: 6, max: 7, minMessage: 'Le titre doit faire au moins 6 caractères', maxMessage: 'Le titre ne peut pas dépasser 7 caractères')]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['sae:read', 'sae:write'])]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(min: 5, minMessage: 'Le nom doit faire au moins 5 caractères')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['sae:read', 'sae:write'])]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Length(min: 10, minMessage: 'La description doit être détaillée (au moins 10 caractères)')]
    private ?string $description = null;

    #[ORM\Column(enumType: Semestre::class)]
    #[Groups(['sae:read', 'sae:write'])]
    #[Assert\NotBlank(message: 'Le semestre est obligatoire')]
    private ?Semestre $semestre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['sae:read', 'sae:write'])]
    #[Assert\NotBlank(message: 'La date de début est obligatoire')]
    #[Assert\GreaterThan('today', message: 'La date de début ne peut pas être dans le passé')]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['sae:read', 'sae:write'])]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire')]
    #[Assert\GreaterThan(propertyPath: 'dateDebut', message: 'La date de fin doit être après la date de début')]
    private ?\DateTime $dateFin = null;

    #[ORM\OneToMany(targetEntity: Rendu::class, mappedBy: 'sae', cascade: ['remove'])]
    private Collection $rendus;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(Semestre $semestre): static
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * @return Collection<int, Rendu>
     */
    public function getRendus(): Collection
    {
        return $this->rendus;
    }

    public function addRendu(Rendu $rendu): static
    {
        if (!$this->rendus->contains($rendu)) {
            $this->rendus->add($rendu);
            $rendu->setSae($this);
        }

        return $this;
    }

    public function removeRendu(Rendu $rendu): static
    {
        if ($this->rendus->removeElement($rendu)) {
            // set the owning side to null (unless already changed)
            if ($rendu->getSae() === $this) {
                $rendu->setSae(null);
            }
        }

        return $this;
    }
}
