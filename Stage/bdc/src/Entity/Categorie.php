<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{

    const NUM_ITEMS = 15;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 128)]
    private ?string $libelle = null;


    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: "categories")]
    #[ORM\JoinTable(name: "article_categorie")]
    #[ORM\JoinColumn(name: "categorie_id", referencedColumnName: "id")]
    #[ORM\inverseJoinColumns(name: "article_id", referencedColumnName: "id")]
    private array|Collection|ArrayCollection $articles;


    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }


    public function __toString()
    {
        return $this->libelle;
    }



    public function getId(): ?int
    {
        return $this->id;
    }


    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
        }

        return $this ;
    }

    public function removeArticle(Article $article): static
    {
        $this->articles->removeElement($article);

        return $this;
    }


}


