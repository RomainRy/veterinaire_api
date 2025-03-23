<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\State\UserPasswordHasherProcessor;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    operations: [
        new GetCollection(),
        new Post(processor: UserPasswordHasherProcessor::class),
        new Get(),
        new Put(processor: UserPasswordHasherProcessor::class),
        new Patch(processor: UserPasswordHasherProcessor::class),
        new Delete(),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', message: 'Cette adresse e-mail est déjà utilisée.')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'adresse e-mail est obligatoire.")]
    #[Assert\Email(message: "L'adresse e-mail n'est pas valide.")]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Groups(['user:read', 'user:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Groups(['user:read', 'user:write'])]
    private ?string $prenom = null;

    #[ORM\Column(name: 'password', length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 6, minMessage: "Le mot de passe doit contenir au moins 6 caractères.")]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank(message: "Le rôle est obligatoire.")]
    #[Assert\All([
        new Assert\Choice(choices: ['ROLE_DIRECTEUR', 'ROLE_VETERINAIRE', 'ROLE_ASSISTANT'], message: "Chaque rôle doit être l'un des suivants : ROLE_DIRECTEUR, ROLE_VETERINAIRE, ROLE_ASSISTANT.")
    ])]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull(message: "La date de création est obligatoire.")]
    #[Groups(['user:read'])]
    private \DateTimeInterface $dateCreation;

    #[Groups(['user:write'])]
    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime(); // Initialisation à la date actuelle
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getUserIdentifier(): string
{
    return (string) $this->email;
}

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}


