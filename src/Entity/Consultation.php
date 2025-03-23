<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use App\Repository\ConsultationRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['consultation:read', 'consultation:detail']],
            security: "is_granted('ROLE_ASSISTANT') or is_granted('ROLE_VETERINAIRE') or is_granted('ROLE_DIRECTOR')",
            securityMessage: "Vous n'avez pas accès à cette consultation."
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['consultation:read']],
            security: "is_granted('ROLE_ASSISTANT') or is_granted('ROLE_VETERINAIRE') or is_granted('ROLE_DIRECTOR')",
            securityMessage: "Vous n'avez pas accès aux consultations."
        ),
        new Post(
            security: "is_granted('ROLE_ASSISTANT')",
            securityMessage: "Seuls les assistants peuvent créer des consultations.",
            denormalizationContext: ['groups' => ['consultation:write']]
        ),
        new Put(
            security: "is_granted('ROLE_ASSISTANT') or (is_granted('ROLE_VETERINAIRE') and object.getStatut() != 'terminé')",
            securityMessage: "Vous n'avez pas les droits pour modifier cette consultation.",
            denormalizationContext: ['groups' => ['consultation:write']]
        ),
        new Delete(
            security: "is_granted('ROLE_DIRECTOR')",
            securityMessage: "Seul le directeur peut supprimer des consultations."
        ),
        new Patch(
            security: "is_granted('ROLE_VETERINAIRE')",
            securityMessage: "Seuls les vétérinaires peuvent s'attribuer un rendez-vous.",
            denormalizationContext: ['groups' => ['consultation:write']],
            normalizationContext: ['groups' => ['consultation:read']]
        )
    ],
    normalizationContext: ['groups' => ['consultation:read']],
    denormalizationContext: ['groups' => ['consultation:write']],
    forceEager: false
)]
#[ApiFilter(DateFilter::class, properties: ['dateCreation', 'dateConsultation'])]
#[ApiFilter(SearchFilter::class, properties: ['statut' => 'exact', 'animal.nom' => 'partial', 'veterinaire.nom' => 'exact'])]
#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
class Consultation
{
    public const STATUT_PROGRAMME = 'programmé';
    public const STATUT_EN_COURS = 'en cours';
    public const STATUT_TERMINE = 'terminé';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['consultation:read', 'animal:detail'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['consultation:read', 'animal:detail'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de consultation est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "dateCreation",
        message: "La date de consultation doit être postérieure à la date de création"
    )]
    #[Groups(['consultation:read', 'consultation:write', 'animal:detail'])]
    private ?\DateTimeInterface $dateConsultation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le motif de la consultation est obligatoire")]
    #[Groups(['consultation:read', 'consultation:write', 'animal:detail'])]
    private ?string $motif = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'animal est obligatoire")]
    #[Groups(['consultation:read', 'consultation:write', 'consultation:detail'])]
    private ?Animal $animal = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'assistant est obligatoire")]
    #[Groups(['consultation:read', 'consultation:write', 'consultation:detail'])]
    private ?User $assistant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['consultation:read', 'consultation:write', 'consultation:detail'])]
    private ?User $veterinaire = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(
        choices: [self::STATUT_PROGRAMME, self::STATUT_EN_COURS, self::STATUT_TERMINE],
        message: "Le statut doit être 'programmé', 'en cours' ou 'terminé'"
    )]
    #[Groups(['consultation:read', 'consultation:write', 'animal:detail'])]
    private string $statut = self::STATUT_PROGRAMME;

    #[ORM\ManyToMany(targetEntity: Traitement::class, inversedBy: 'consultations')]
    #[Groups(['consultation:detail', 'consultation:write'])]
    private Collection $traitements;

    #[ORM\Column]
    #[Groups(['consultation:read', 'consultation:detail'])]
    private bool $estPaye = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['consultation:read', 'consultation:detail', 'consultation:write'])]
    private ?string $montantTotal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['consultation:detail', 'consultation:write'])]
    private ?string $notes = null;

    public function __construct()
    {
        $this->traitements = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->statut = self::STATUT_PROGRAMME;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateConsultation(): ?\DateTimeInterface
    {
        return $this->dateConsultation;
    }

    public function setDateConsultation(\DateTimeInterface $dateConsultation): static
    {
        $this->dateConsultation = $dateConsultation;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getAssistant(): ?User
    {
        return $this->assistant;
    }

    public function setAssistant(?User $assistant): static
    {
        $this->assistant = $assistant;

        return $this;
    }

    public function getVeterinaire(): ?User
    {
        return $this->veterinaire;
    }

    public function setVeterinaire(?User $veterinaire): static
    {
        $this->veterinaire = $veterinaire;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Traitement>
     */
    public function getTraitements(): Collection
    {
        return $this->traitements;
    }

    public function addTraitement(Traitement $traitement): static
    {
        if (!$this->traitements->contains($traitement)) {
            $this->traitements->add($traitement);
        }

        return $this;
    }

    public function removeTraitement(Traitement $traitement): static
    {
        $this->traitements->removeElement($traitement);

        return $this;
    }

    public function isEstPaye(): ?bool
    {
        return $this->estPaye;
    }

    public function setEstPaye(bool $estPaye): static
    {
        $this->estPaye = $estPaye;

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(?string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Calcule le montant total basé sur les traitements
     */
    public function calculerMontantTotal(): float
    {
        $total = 0.0;
        foreach ($this->traitements as $traitement) {
            $total += $traitement->getPrix();
        }
        $this->montantTotal = (string) $total;
        return $total;
    }
}
