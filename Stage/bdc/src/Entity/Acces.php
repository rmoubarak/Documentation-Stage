<?php

namespace App\Entity;

use App\Repository\AccesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'acces')]
#[ORM\Entity(repositoryClass: AccesRepository::class)]
class Acces
{
    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $date;

    #[ORM\Column()]
    private ?string $ip = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'access')]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "id")]
    private Utilisateur $utilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }
}
