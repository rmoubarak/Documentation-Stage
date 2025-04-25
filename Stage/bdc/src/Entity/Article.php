<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    const NUM_ITEMS = 15;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message:"La date de création est obligatoire.")]
    private ?\DateTimeInterface $created_at = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le titre est obligatoire.")]
    private ?string $titre = null;
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:"Le détail est obligatoire.")]
    private ?string $detail = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "id")]
    private Utilisateur $utilisateur;
    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: "articles")]
    private array|Collection|ArrayCollection $categories;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }



    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategorie(Categorie $categorie): static
    {
        if (!$this->categories->contains($categorie)) {
            $this->categories->add($categorie);
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): static
    {
        $this->categories->removeElement($categorie);

        return $this;
    }
}
