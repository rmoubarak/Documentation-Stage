<?php

namespace App\Services;

use App\Entity\Direction;
use App\Entity\Pole;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Agent
{
    public function __construct(
        private string $url,
        private HttpClientInterface $client,
        private EntityManagerInterface $entityManager
    )
    {}

    /**
     * Récupère le N+1 d'un agent via Web Service
     *
     * @param string $matricule
     * @return array|false
     */
    public function getN1(string $matricule): array|false
    {
        $response = $this->client->request('GET', $this->url . $matricule);

        if ($response->getStatusCode() == 200) {
            $agent = $response->toArray();

            if ($agent['root']['resultat']['code'] == 1) {
                return $agent['root']['responsable'];
            }
        }

        return false;
    }

    /**
     * Enregistrement en base d'un nouvel utilisateur
     *
     * @param string $login
     * @param bool $addN1
     * @return Utilisateur|bool
     */
    public function add(string $login = null, bool $addN1 = true, string $email = null): Utilisateur|null
    {
        $user = null;

        if ($login) {
            $user = $this->ldap->findUserByLogin($login);
        } elseif ($email) {
            $user = $this->ldap->findUserByEmail($email);
        }

        if (false === $this->ldap->checkUser($user)) {
            return null;
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setCreatedAt(new \DateTime());
        $utilisateur->setUpdatedAt(new \DateTime());
        $utilisateur->setLogin($this->ldap->getLogin($user));
        $utilisateur->setCivilite($this->ldap->getCivilite($user));
        $utilisateur->setNom($this->ldap->getNom($user));
        $utilisateur->setPrenom($this->ldap->getPrenom($user));
        $utilisateur->setEmail($this->ldap->getEmail($user));
        $utilisateur->setTelephone($this->ldap->getTelephone($user));
        $utilisateur->setMatricule($this->ldap->getMatricule($user));
        $utilisateur->setRole('Utilisateur');

        $direction_sigle = $this->ldap->getDirectionSigle($user);
        if ($direction_sigle) {
            $direction = $this->entityManager->getRepository(Direction::class)->findOneBy(['sigle' => $direction_sigle]);
            if ($direction) {
                $utilisateur->setDirection($direction);
            }
        }

        // Ajout du N+1
        if ($addN1) {
            $matricule = preg_replace("/[^0-9]/", '', $utilisateur->getMatricule());
            $n1 = $this->getN1($matricule);

            if ($n1) {
                // Si le N+1 n'existe pas, on le crée (sans son N+1)
                $utilisateurN1 = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['login' => $n1['LOGIN_CHEF']]);

                if (!$utilisateurN1) {
                    $utilisateurN1 = $this->add($n1['LOGIN_CHEF'], false);
                }

                $utilisateur->setN1($utilisateurN1);
            }
        }

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return $utilisateur;
    }

    /**
     * MAJ en base d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return Utilisateur|bool
     */
    public function update(Utilisateur $utilisateur): Utilisateur|null
    {
        $user = $this->ldap->findUserByLogin($utilisateur->getLogin());

        if (false === $this->ldap->checkUser($user)) {
            return null;
        }

        $utilisateur->setUpdatedAt(new \DateTime());
        $utilisateur->setNom($this->ldap->getNom($user));
        $utilisateur->setPrenom($this->ldap->getPrenom($user));
        $utilisateur->setEmail($this->ldap->getEmail($user));
        $utilisateur->setTelephone($this->ldap->getTelephone($user));
        $utilisateur->setMatricule($this->ldap->getMatricule($user));
        $utilisateur->setFonction($this->ldap->getFonction($user));

        $direction_sigle = $this->ldap->getDirectionSigle($user);
        if ($direction_sigle) {
            $direction = $this->entityManager->getRepository(Direction::class)->findOneBy(['sigle' => $direction_sigle]);
            if ($direction) {
                $utilisateur->setDirection($direction);

                if ($direction->getPole()) {
                    $utilisateur->setPole($direction->getPole());
                }
            } else {
                // Parfois c'est un pôle ...
                $pole = $this->entityManager->getRepository(Pole::class)->findOneBy(['sigle' => $direction_sigle]);
                if ($pole) {
                    $utilisateur->setPole($pole);
                }
            }
        }

        // MAJ du N+1
        $matricule = preg_replace("/[^0-9]/", '', $utilisateur->getMatricule());
        $n1 = $this->getN1($matricule);

        if ($n1) {
            // Si le N+1 n'existe pas, on le crée (sans son N+1)
            $utilisateurN1 = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['login' => $n1['LOGIN_CHEF']]);

            if (!$utilisateurN1) {
                $utilisateurN1 = $this->add($n1['LOGIN_CHEF'], false);
            }

            $utilisateur->setN1($utilisateurN1);
        }

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return $utilisateur;
    }
}
