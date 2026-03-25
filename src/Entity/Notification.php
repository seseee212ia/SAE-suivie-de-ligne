<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['notification:read']],
    denormalizationContext: ['groups' => ['notification:write']],
    operations: [
        new \ApiPlatform\Metadata\GetCollection(security: "is_granted('ROLE_ENSEIGNANT') or is_granted('ROLE_ADMIN')"),
        new \ApiPlatform\Metadata\Get(security: "is_granted('ROLE_ENSEIGNANT') or is_granted('ROLE_ADMIN')"),
        new \ApiPlatform\Metadata\Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(targetEntity: Enseignants::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?Enseignants $enseignant = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
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
