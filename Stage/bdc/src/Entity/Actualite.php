<?php

namespace App\Entity;

use App\Repository\ActualiteRepository;
use App\Validator as ActualiteAssert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'actualite')]
#[ORM\Entity(repositoryClass: ActualiteRepository::class)]
#[Vich\Uploadable]
class Actualite
{
    public const NUM_ITEMS = 20;
    public const STATUTS = ['En ligne', 'Hors ligne'];

    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column()]
    private \DateTime $createdAt;

    #[ORM\Column()]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(type: 'string', length: 128)]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $titre;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $libelle;

    #[ORM\Column(type: 'string', length: 32)]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $statut;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'actualites', fileNameProperty: 'fichier')]
    #[ActualiteAssert\ClamavScan()]
    private ?File $file = null;

    #[ORM\Column()]
    private ?string $fichier = null;

    public function __toString()
    {
        return $this->libelle;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $file
     */
    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }
}