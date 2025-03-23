<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    normalizationContext: ['groups' => ['media:read']],
    denormalizationContext: ['groups' => ['media:write']],
    forceEager: false
)]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Vich\Uploadable]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['media:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le chemin du fichier est obligatoire.")]
    #[Assert\Url(message: "Le chemin doit être une URL valide.")]
    #[Groups(['media:read', 'media:write'])]
    private ?string $chemin = null;

    // Ajout d'une relation avec Consultation, par exemple
    #[ORM\ManyToOne(targetEntity: Consultation::class, inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['media:read', 'media:write'])]
    private ?Consultation $consultation = null;

    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'chemin')]
    private ?\Symfony\Component\HttpFoundation\File\File $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChemin(): ?string
    {
        return $this->chemin;
    }

    public function setChemin(string $chemin): static
    {
        $this->chemin = $chemin;

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

    public function getFile(): ?\Symfony\Component\HttpFoundation\File\File
    {
        return $this->file;
    }

    public function setFile(?\Symfony\Component\HttpFoundation\File\File $file): static
    {
        $this->file = $file;

        if ($file) {
            $this->chemin = $file->getClientOriginalName();
        }

        return $this;
    }
}

