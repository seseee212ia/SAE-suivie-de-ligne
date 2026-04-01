<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\DocumentController;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['doc:read']],
    denormalizationContext: ['groups' => ['doc:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: DocumentController::class,
            deserialize: false,
            security: "is_granted('ROLE_ENSEIGNANT')",
            inputFormats: ['multipart' => ['multipart/form-data']]
        ),
        new Put(security: "is_granted('ROLE_ENSEIGNANT')"),
        new Delete(security: "is_granted('ROLE_ENSEIGNANT')")
    ]
)]
class Documents
{
    #[Groups(['doc:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['doc:read', 'doc:write'])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[Groups(['doc:read', 'doc:write'])]
    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Sae $sae = null;

    #[Groups(['doc:read', 'doc:write'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Enseignants $enseignant = null;
    
    #[Groups(['doc:write'])] 
    public ?File $file = null; 
 
    #[ORM\Column(nullable: true)] 
    #[Groups(['doc:read'])] 
    private ?string $fileName = null; 

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
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

    public function getFile(): ?File { 
        return $this->file; 
    }

    public function getFileName(): ?string { 
        return $this->fileName; 
    } 

    public function setFileName(?string $fileName): void 
    { 
        $this->fileName = $fileName; 
    }
}