<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['document:read']],
    denormalizationContext: ['groups' => ['document:write']],
    operations: [
        new \ApiPlatform\Metadata\GetCollection(),
        new \ApiPlatform\Metadata\Get(),
        new \ApiPlatform\Metadata\Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            security: "is_granted('ROLE_ENSEIGNANT') or is_granted('ROLE_ADMIN')"
        ),
        new \ApiPlatform\Metadata\Put(
            security: "is_granted('ROLE_ENSEIGNANT') or is_granted('ROLE_ADMIN')"
        ),
        new \ApiPlatform\Metadata\Delete(
            security: "is_granted('ROLE_ENSEIGNANT') or is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[Vich\Uploadable]
class Documents
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['document:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['document:read', 'document:write'])]
    #[Assert\NotBlank(message: 'Le nom du document est obligatoire')]
    #[Assert\Length(min: 3, minMessage: 'Le nom doit faire au moins 3 caractères')]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:read', 'document:write'])]
    #[Assert\NotNull(message: 'Une SAE doit être rattachée')]
    private ?Sae $sae = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:read', 'document:write'])]
    #[Assert\NotNull(message: 'Un enseignant doit être rattaché')]
    private ?Enseignants $enseignant = null;

    #[Vich\UploadableField(mapping: 'rendus', fileNameProperty: 'nomFichier')]
    #[Groups(['document:write'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?string $nomFichier = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['document:read', 'document:write'])]
    private ?string $format = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['document:read', 'document:write'])]
    private ?string $lien = null;

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

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(?string $nomFichier): void
    {
        $this->nomFichier = $nomFichier;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): static
    {
        $this->lien = $lien;
        return $this;
    }
}
