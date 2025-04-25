<?php

namespace App\Services;

use App\Entity\Direction;
use App\Entity\Pole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Structure
{
    public function __construct(
        private string $url,
        private HttpClientInterface $client,
        private EntityManagerInterface $entityManager
    )
    {}

    public function load()
    {
        $response = $this->client->request('GET', $this->url, [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
        ]);

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];

        if ($statusCode != 200 || $contentType != 'application/xml') {
            return false;
        }

        return new \SimpleXMLElement($response->getContent());
    }

    /**
     * Ajout des structures manquantes en bdd
     *
     * @param \SimpleXMLElement $poles
     * @return int[]
     */
    public function add(\SimpleXMLElement $poles): array
    {
        $nbPoles = 0;
        $nbDirections = 0;
        foreach ($poles as $pole) {
            $pole_id = (int)$pole['CODE_POLE'];
            $pole_libelle = (string)$pole['NOM_POLE'];
            $pole_sigle = (string)$pole['SIGLE_POLE'];

            // Si le pôle n'existe pas, on le crée
            $app_pole = $pole_id ? $this->entityManager->getRepository(Pole::class)->find($pole_id) : null;
            if ($pole_id && !$app_pole) {
                $app_pole = new Pole();
                $app_pole->setId($pole_id);
                $app_pole->setCreatedAt(new \DateTime());
                $app_pole->setLibelle($pole_libelle);
                $app_pole->setSigle($pole_sigle);
                $app_pole->setActif(true);

                $this->entityManager->persist($app_pole);
                $nbPoles++;
                // S'il existe et est désactivé, on le réactive
            } else if ($app_pole && $app_pole->getActif() == false) {
                $app_pole->setUpdatedAt(new \DateTime());
                $app_pole->setLibelle($pole_libelle);
                $app_pole->setSigle($pole_sigle);
                $app_pole->setActif(true);

                $this->entityManager->persist($app_pole);
                $nbPoles++;
            }

            foreach ($pole->directions as $direction) {
                $direction_id = (string)$direction['CODE_DIRECTION'];
                $direction_libelle = (string)$direction['NOM_DIRECTION'];
                $direction_sigle = (string)$direction['SIGLE_DIRECTION'];

                // Si la direction n'existe pas, on la crée
                $app_direction = $direction_id ? $this->entityManager->getRepository(Direction::class)->find($direction_id) : null;
                if ($direction_id && !$app_direction) {
                    $app_direction = new Direction();
                    $app_direction->setId($direction_id);
                    $app_direction->setPole($app_pole);
                    $app_direction->setCreatedAt(new \DateTime());
                    $app_direction->setLibelle($direction_libelle);
                    $app_direction->setSigle($direction_sigle);
                    $app_direction->setActif(true);

                    $this->entityManager->persist($app_direction);
                    $nbDirections++;
                    // Si elle existe et est désactivée, on le réactive
                } else if ($app_direction && $app_direction->getActif() == false) {
                    $app_direction->setUpdatedAt(new \DateTime());
                    $app_direction->setPole($app_pole);
                    $app_direction->setLibelle($direction_libelle);
                    $app_direction->setSigle($direction_sigle);
                    $app_direction->setActif(true);

                    $this->entityManager->persist($app_direction);
                    $nbDirections++;
                }
            }
        }

        if ($nbPoles || $nbDirections != 0) {
            $this->entityManager->flush();
        }

        return [
            'nbPoles' => $nbPoles,
            'nbDirections' => $nbDirections,
        ];
    }

    /**
     * Désactive en bdd les structures absentes dans le web service
     *
     * @param \SimpleXMLElement $poles
     * @return array|bool
     */
    public function remove(\SimpleXMLElement $poles): array|bool
    {
        $nbPoles = 0;
        $nbDirections = 0;
        $pole_ids = [];
        $direction_ids = [];

        // On stocke les structures existantes pour comparaison avec ce qu'on a en bdd
        foreach ($poles as $pole) {
            $pole_ids[] = (int)$pole['CODE_POLE'];

            foreach ($pole->directions as $direction) {
                $direction_ids[] = (string)$direction['CODE_DIRECTION'];
            }
        }

        // On ne désactive rien si le web service ne renvoie pas au moins 2 pôles et 2 directions ...
        if (count($pole_ids) < 2 || count($direction_ids) < 2) {
            return false;
        }

        $app_directions = $this->entityManager->getRepository(Direction::class)->findBy(['actif' => true]);
        foreach ($app_directions as $direction) {
            if (!in_array($direction->getId(), $direction_ids)) {
                $direction->setActif(false);
                $direction->setDeletedAt(new \DateTime());

                $nbDirections++;
            }
        }

        $app_poles = $this->entityManager->getRepository(Pole::class)->findBy(['actif' => true]);
        foreach ($app_poles as $pole) {
            if (!in_array($pole->getId(), $pole_ids)) {
                $pole->setActif(false);
                $pole->setDeletedAt(new \DateTime());

                $nbPoles++;
            }
        }

        if ($nbPoles || $nbDirections != 0) {
            $this->entityManager->flush();
        }

        return [
            'nbPoles' => $nbPoles,
            'nbDirections' => $nbDirections,
        ];
    }

    /**
     * MAJ en bdd des structures modifiées dans le web service
     *
     * @param \SimpleXMLElement $poles
     * @return array|bool
     */
    public function update(\SimpleXMLElement $poles): array|bool
    {
        $nbPoles = 0;
        $nbDirections = 0;
        $pole_libelles = [];
        $pole_sigles = [];
        $direction_libelles = [];
        $direction_sigles = [];

        // On stocke les structures existantes pour comparaison avec ce qu'on a en bdd
        foreach ($poles as $pole) {
            $pole_id = (int)$pole['CODE_POLE'];

            $pole_libelles[$pole_id] = (string)$pole['NOM_POLE'];
            $pole_sigles[$pole_id] = (string)$pole['SIGLE_POLE'];

            foreach ($pole->directions as $direction) {
                $direction_id = (string)$direction['CODE_DIRECTION'];

                $direction_libelles[$direction_id] = (string)$direction['NOM_DIRECTION'];
                $direction_sigles[$direction_id] = (string)$direction['SIGLE_DIRECTION'];
            }
        }

        // On ne modifie rien si le web service ne renvoie pas au moins 2 pôles et 2 directions ...
        if (count($pole_libelles) < 2 || count($direction_libelles) < 2) {
            return false;
        }

        $app_directions = $this->entityManager->getRepository(Direction::class)->findBy(['actif' => true]);
        foreach ($app_directions as $direction) {
            // On récupère les libellés et sigles du Web Service et on compare avec ce qu'on a en base
            $ws_libelle = array_key_exists($direction->getId(), $direction_libelles) ? $direction_libelles[$direction->getId()] : null;
            $ws_sigle = array_key_exists($direction->getId(), $direction_sigles) ? $direction_sigles[$direction->getId()] : null;

            if ($ws_libelle && $ws_libelle != $direction->getLibelle()) {
                $direction->setLibelle($ws_libelle);
                $direction->setSigle($ws_sigle);
                $direction->setUpdatedAt(new \DateTime());

                $nbDirections++;
            }
        }

        $app_poles = $this->entityManager->getRepository(Pole::class)->findBy(['actif' => true]);
        foreach ($app_poles as $pole) {
            $ws_libelle = array_key_exists($pole->getId(), $pole_libelles) ? $pole_libelles[$pole->getId()] : null;
            $ws_sigle = array_key_exists($pole->getId(), $pole_sigles) ? $pole_sigles[$pole->getId()] : null;

            if ($ws_libelle && $ws_libelle != $pole->getLibelle()) {
                $pole->setLibelle($ws_libelle);
                $pole->setSigle($ws_sigle);
                $pole->setUpdatedAt(new \DateTime());

                $nbPoles++;
            }
        }

        if ($nbPoles || $nbDirections != 0) {
            $this->entityManager->flush();
        }

        return [
            'nbPoles' => $nbPoles,
            'nbDirections' => $nbDirections,
        ];
    }
}