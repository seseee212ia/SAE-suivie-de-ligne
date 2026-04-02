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
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RenduRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['rendu:read']],
    denormalizationContext: ['groups' => ['rendu:write']],
    operations: [
        new \ApiPlatform\Metadata\GetCollection(),
        new \ApiPlatform\Metadata\Get(),
        new \ApiPlatform\Metadata\Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            security: "is_granted('ROLE_ETUDIANT') or is_granted('ROLE_ADMIN')"
        ),
        new \ApiPlatform\Metadata\Put(
            security: "is_granted('ROLE_ETUDIANT') or is_granted('ROLE_ADMIN')"
        ),
        new \ApiPlatform\Metadata\Delete(
            security: "is_granted('ROLE_ETUDIANT') or is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[Vich\Uploadable]
class Rendu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rendu:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotBlank(message: 'Le nom du fichier est obligatoire')]
    #[Assert\Length(min: 3, minMessage: 'Le nom du fichier doit faire au moins 3 caractères')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotBlank(message: 'La date est obligatoire')]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotNull(message: 'Une SAE doit obligatoirement être attachée')]
    private ?Sae $sae = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rendu:read', 'rendu:write'])]
    #[Assert\NotNull(message: 'Un étudiant (auteur) doit obligatoirement être attaché')]
    private ?Etudiants $etudiant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['rendu:read', 'rendu:write'])]
    private ?Groupe $groupe = null;

    #[Vich\UploadableField(mapping: 'rendus', fileNameProperty: 'fileName')]
    #[Groups(['rendu:write'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['rendu:read'])]
    private ?string $fileName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
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

    public function setFile(?File $file = null): void
    {
        $this->file = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }
}
