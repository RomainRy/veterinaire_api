<?php
namespace App\Entity;

use App\Repository\TraitementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['traitement:read']],
    denormalizationContext: ['groups' => ['traitement:write']],
    forceEager: false
)]
#[ORM\Entity(repositoryClass: TraitementRepository::class)]
class Traitement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['traitement:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du traitement est obligatoire.")]
    #[Groups(['traitement:read', 'traitement:write'])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description du traitement est obligatoire.")]
    #[Groups(['traitement:read', 'traitement:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le prix du traitement est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    #[Groups(['traitement:read', 'traitement:write'])]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La durée du traitement est obligatoire.")]
    #[Assert\Regex(pattern: "/^\d+(\.\d{1,2})?$/", message: "La durée doit être un nombre valide.")]
    #[Groups(['traitement:read', 'traitement:write'])]
    private ?string $durée = null;

    // Ajout d'une relation avec une autre entité si nécessaire (exemple : Consultation)
    #[ORM\ManyToOne(targetEntity: Consultation::class, inversedBy: 'traitements')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['traitement:read', 'traitement:write'])]
    private ?Consultation $consultation = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDurée(): ?string
    {
        return $this->durée;
    }

    public function setDurée(string $durée): static
    {
        $this->durée = $durée;

        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        return $this;
    }
}

