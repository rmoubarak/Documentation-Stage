<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pole')]
#[ORM\Entity]
class Pole
{
    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column]
    private \DateTime $createdAt;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    private ?\DateTime $deletedAt = null;

    #[ORM\Column(length: 64)]
    private string $libelle;

    #[ORM\Column(length: 8)]
    private string $sigle;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'pole', targetEntity: Direction::class)]
    private array|Collection|ArrayCollection $directions;

    public function __construct()
    {
        $this->directions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return  $this->libelle;
    }

    public function getActivesDirections(Utilisateur $utilisateur = null): array
    {
        $dirs = [];

        foreach ($this->getDirections() as $direction) {
            if ($direction->getActif() || ($utilisateur && $utilisateur->getDirection() == $direction)) {
                $dirs[] = $direction;
            }
        }

        return $dirs;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(string $sigle): self
    {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * @return Collection<int, Direction>
     */
    public function getDirections(): Collection
    {
        return $this->directions;
    }

    public function addDirection(Direction $direction): self
    {
        if (!$this->directions->contains($direction)) {
            $this->directions[] = $direction;
            $direction->setPole($this);
        }

        return $this;
    }

    public function removeDirection(Direction $direction): self
    {
        if ($this->directions->removeElement($direction)) {
            // set the owning side to null (unless already changed)
            if ($direction->getPole() === $this) {
                $direction->setPole(null);
            }
        }

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }
}
