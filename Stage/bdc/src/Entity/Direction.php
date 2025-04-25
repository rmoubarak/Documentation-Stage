<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'direction')]
#[ORM\Entity]
class Direction
{
    #[ORM\Id]
    #[ORM\Column(length: 3)]
    private string $id;

    #[ORM\Column]
    private \DateTime $createdAt;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    private ?\DateTime $deletedAt = null;

    #[ORM\Column(length: 128)]
    private string $libelle;

    #[ORM\Column(length: 8)]
    private string $sigle;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\ManyToOne(targetEntity: Pole::class, inversedBy:"directions")]
    #[ORM\JoinColumn(name: "pole_id", referencedColumnName: "id")]
    private Pole $pole;

    public function __toString(): string
    {
        return  $this->libelle;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
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

    public function getPole(): ?Pole
    {
        return $this->pole;
    }

    public function setPole(?Pole $pole): self
    {
        $this->pole = $pole;

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
