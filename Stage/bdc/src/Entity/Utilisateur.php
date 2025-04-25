<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'utilisateur')]
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity('login', message: 'Identifiant déjà utilisé')]
#[UniqueEntity('email', message: 'Email déjà utilisé')]
#[UniqueEntity('matricule', message: 'Matricule déjà utilisé')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    const NUM_ITEMS = 15;
    const ROLES = ['Utilisateur', 'Gestionnaire', 'Public', 'Administrateur'];
    const BOOLS = [1 => 'Oui', 0 => 'Non'];
    const CIVILITES = ['Mme', 'M.'];

    #[ORM\Id]
    #[ORM\Column(options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column]
    private \DateTime $createdAt;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $civilite;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $nom;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $prenom;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $login = '';

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    #[Assert\Email(message: "Adresse email non valide")]
    private string $email;

    #[ORM\Column]
    #[Assert\Length(min: 12, minMessage: "Veuillez saisir 12 caractères minimum", groups: ["pwd"])]
    #[Assert\Regex(pattern: "/\d/", message: "Veuillez saisir au moins un chiffre", groups: ["pwd"])]
    #[Assert\Regex(pattern: "/[A-Z]/", message: "Veuillez saisir au moins une lettre majuscule", groups: ["pwd"])]
    #[Assert\Regex(pattern: "/[a-z]/", message: "Veuillez saisir au moins une lettre minuscule", groups: ["pwd"])]
    private ?string $password = null;

    #[ORM\Column(length: 15)]
    private ?string $telephone = null;

    #[ORM\Column(length: 32)]
    private ?string $matricule = null;

    #[ORM\Column(length: 128)]
    private ?string $fonction = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Champ obligatoire")]
    private string $role = 'Utilisateur';

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private ?string $authCode = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?\DateTime $tokenDate = null;

    #[ORM\Column]
    private bool $showMenu = true;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "n1_utilisateur_id", referencedColumnName: "id")]
    private ?Utilisateur $n1 = null;

    #[ORM\ManyToOne(targetEntity: Direction::class)]
    #[ORM\JoinColumn(name: "direction_id", referencedColumnName: "id")]
    private ?Direction $direction = null;

    #[ORM\ManyToOne(targetEntity: Pole::class)]
    #[ORM\JoinColumn(name: "pole_id", referencedColumnName: "id")]
    private ?Pole $pole = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Acces::class)]
    #[ORM\OrderBy(['date' => 'DESC'])]
    private array|Collection|ArrayCollection $access;

    #[Pure]
    public function __construct()
    {
        $this->access = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->civilite . ' ' . $this->nom . ' ' . $this->prenom;
    }

    /**
     * Détermine s'il s'agit d'un utilisateur avec authentification locale ou AD
     *
     * @return bool
     */
    public function isCompteLocal(): bool
    {
        return $this->login == $this->email;
    }

    public function getLastConnectionDate(): ?\DateTimeInterface
    {
        return $this->getAccess()[0]?->getDate();
    }

    public function isEmailAuthEnabled(): bool
    {
        return true; // This can be a persisted field to switch email code authentication on/off
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return Role[] The user roles
     */
    public function getRoles(): array
    {
        $roles = array();

        if ($this->role == 'Utilisateur') {
            $roles[] = 'ROLE_USER';
        } else if ($this->role == 'Gestionnaire') {
            $roles[] = 'ROLE_GESTIONNAIRE';
            $roles[] = 'ROLE_USER';
        } else if ($this->role == 'Public') {
            $roles[] = 'ROLE_PUBLIC';
        } else if ($this->role == 'Administrateur') {
            $roles[] = 'ROLE_ADMIN';
            $roles[] = 'ROLE_USER';
        } else if ($this->role == 'Super administrateur') {
            $roles[] = 'ROLE_SUPER_ADMIN';
            $roles[] = 'ROLE_ALLOWED_TO_SWITCH';
            $roles[] = 'ROLE_ADMIN';
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->login;
    }

    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    public function setNom(string $nom): self
    {
        $this->nom = mb_strtoupper($nom);

        return $this;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = ucfirst($prenom);

        return $this;
    }


    /*** SUPPRIMER A PARTIR D'ICI ***/


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(Int $id): self
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

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

    /**
     * @return Collection|Acces[]
     */
    public function getAccess(): Collection
    {
        return $this->access;
    }

    public function addAccess(Acces $access): self
    {
        if (!$this->access->contains($access)) {
            $this->access[] = $access;
            $access->setUtilisateur($this);
        }

        return $this;
    }

    public function removeAccess(Acces $access): self
    {
        if ($this->access->removeElement($access)) {
            // set the owning side to null (unless already changed)
            if ($access->getUtilisateur() === $this) {
                $access->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getDirection(): ?Direction
    {
        return $this->direction;
    }

    public function setDirection(?Direction $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function setAuthCode(string $authCode): self
    {
        $this->authCode = $authCode;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenDate(): ?\DateTimeInterface
    {
        return $this->tokenDate;
    }

    public function setTokenDate(?\DateTimeInterface $tokenDate): self
    {
        $this->tokenDate = $tokenDate;

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

    public function getN1(): ?self
    {
        return $this->n1;
    }

    public function setN1(?self $n1): static
    {
        $this->n1 = $n1;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function isShowMenu(): ?bool
    {
        return $this->showMenu;
    }

    public function setShowMenu(bool $showMenu): static
    {
        $this->showMenu = $showMenu;

        return $this;
    }
}
