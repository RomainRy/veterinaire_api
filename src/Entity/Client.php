<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\ClientRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['client:read', 'client:detail']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['client:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ASSISTANT')",
            securityMessage: "Seuls les assistants peuvent créer des clients.",
            denormalizationContext: ['groups' => ['client:write']]
        ),
        new Put(
            security: "is_granted('ROLE_ASSISTANT')",
            securityMessage: "Seuls les assistants peuvent modifier des clients.",
            denormalizationContext: ['groups' => ['client:write']]
        ),
        new Delete(
            security: "is_granted('ROLE_DIRECTOR')",
            securityMessage: "Seul le directeur peut supprimer des clients."
        )
    ],
    normalizationContext: ['groups' => ['client:read']],
    denormalizationContext: ['groups' => ['client:write']],
    forceEager: false
)]
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read', 'animal:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du client est obligatoire")]
    #[Groups(['client:read', 'client:write', 'animal:detail'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom du client est obligatoire")]
    #[Groups(['client:read', 'client:write', 'animal:detail'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: "L'adresse email doit être valide")]
    #[Groups(['client:read', 'client:write', 'client:detail'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: "/^[0-9]{10}$/",
        message: "Le numéro de téléphone doit contenir 10 chiffres"
    )]
    #[Groups(['client:read', 'client:write', 'client:detail'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['client:read', 'client:write', 'client:detail'])]
    private ?string $adresse = null;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Animal::class)]
    #[Groups(['client:detail'])]
    private Collection $animaux;

    public function __construct()
    {
        $this->animaux = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimaux(): Collection
    {
        return $this->animaux;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animaux->contains($animal)) {
            $this->animaux->add($animal);
            $animal->setProprietaire($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animaux->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getProprietaire() === $this) {
                $animal->setProprietaire(null);
            }
        }

        return $this;
    }
}
