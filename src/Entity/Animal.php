<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\AnimalRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['animal:read', 'animal:detail']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['animal:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ASSISTANT')",
            securityMessage: "Seuls les assistants peuvent créer des fiches animal.",
            denormalizationContext: ['groups' => ['animal:write']]
        ),
        new Put(
            security: "is_granted('ROLE_ASSISTANT')",
            securityMessage: "Seuls les assistants peuvent modifier des fiches animal.",
            denormalizationContext: ['groups' => ['animal:write']]
        ),
        new Delete(
            security: "is_granted('ROLE_DIRECTOR')",
            securityMessage: "Seul le directeur peut supprimer des fiches animal."
        )
    ],
    normalizationContext: ['groups' => ['animal:read']],
    denormalizationContext: ['groups' => ['animal:write']],
    forceEager: false
)]
#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['animal:read', 'consultation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'animal est obligatoire")]
    #[Groups(['animal:read', 'animal:write', 'consultation:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'espèce de l'animal est obligatoire")]
    #[Groups(['animal:read', 'animal:write', 'consultation:read'])]
    private ?string $espece = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de naissance de l'animal est obligatoire")]
    #[Assert\LessThanOrEqual("today", message: "La date de naissance ne peut pas être dans le futur")]
    #[Groups(['animal:read', 'animal:write', 'consultation:read'])]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La photo de l'animal est obligatoire")]
    #[Groups(['animal:read', 'animal:write', 'animal:detail'])]
    private ?Media $photo = null;

    #[ORM\ManyToOne(inversedBy: 'animaux')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le propriétaire de l'animal est obligatoire")]
    #[Groups(['animal:read', 'animal:write', 'animal:detail'])]
    private ?Client $proprietaire = null;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: Consultation::class, orphanRemoval: true)]
    #[Groups(['animal:detail'])]
    private Collection $consultations;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
    }

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

    public function getEspece(): ?string
    {
        return $this->espece;
    }

    public function setEspece(string $espece): static
    {
        $this->espece = $espece;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPhoto(): ?Media
    {
        return $this->photo;
    }

    public function setPhoto(Media $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getProprietaire(): ?Client
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Client $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): static
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setAnimal($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getAnimal() === $this) {
                $consultation->setAnimal(null);
            }
        }

        return $this;
    }
}

