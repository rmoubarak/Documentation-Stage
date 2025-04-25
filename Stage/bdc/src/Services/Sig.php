<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Sig
{
    public function __construct(private HttpClientInterface $client)
    {}

    /**
     * Requête API gouv pour récupération des adresses postales
     *
     * @param string $q
     * @param string $code_insee
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function findAdresses(string $q, string $code_insee = ''): array
    {
        if ($code_insee) {
            $url = "https://api-adresse.data.gouv.fr/search/?limit=99&citycode=$code_insee&q=$q";
        } else {
            $url = "https://api-adresse.data.gouv.fr/search/?limit=99&q=$q";
        }

        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() == 200) {
            return $this->formatAdresseResponse($response->toArray());
        }

        return [];
    }

    /**
     * Formatage de la réponse
     *
     * "properties" => [
     *   "label" => "69 Rue Rene Boileau 80090 Amiens"
     *   "score" => 0.88943181818182
     *   "housenumber" => "69"
     *   "id" => "80021_6770_00069"
     *   "name" => "69 Rue Rene Boileau"
     *   "postcode" => "80090"
     *   "citycode" => "80021"
     *   "x" => 651970.39
     *   "y" => 6975457.09
     *   "city" => "Amiens"
     *   "context" => "80, Somme, Hauts-de-France"
     *   "type" => "housenumber" // peut être "street" si pas de numéro saisie dans l'adresse
     *   "importance" => 0.78375
     *   "street" => "Rue Rene Boileau"
     * ]
     *
     * @param array $response
     * @return json
     */
    private function formatAdresseResponse(array $response): array
    {
        $adresses = [];

        if ($response['features']) {
            foreach ($response['features'] as $key => $item) {
                $adresses[$key] = $item['properties'];
                $adresses[$key]['longitude'] = $item['geometry']['coordinates'][0];
                $adresses[$key]['latitude'] = $item['geometry']['coordinates'][1];
            }
        }

        return $adresses;
    }

    /**
     * Calcul de distance entre deux points identifiés par leurs longitude et latitude
     * swagger : https://www.geoportail.gouv.fr/depot/swagger/itineraire.html
     *
     * @param float $startX
     * @param float $startY
     * @param float $endX
     * @param float $endY
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function calculItineraire(float $startX, float $startY, float $endX, float $endY): string
    {
        $url = "https://data.geopf.fr/navigation/itineraire?resource=bdtopo-osrm&start=$startX,$startY&end=$endX,$endY";
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() == 200) {
            return json_encode($response->toArray());
        }

        return '';
    }

    /**
     * Retourne le QPV si l'adresse en fait partie ; vide sinon
     *
     * @param string $code_postal
     * @param string $commune
     * @param string $adresse
     * @return string
     *
     * Réponse type :
     * [
     * "type_quartier" => "QP"
     * "code_reponse" => "OUI"
     * "nom_quartier" => "Marcel Paul - Salamandre"
     * "code_quartier" => "QN08004M"
     * "code_info" => "VOIE_TRAITEE"
     * "info" => "L'adresse est située dans le quartier QP Marcel Paul - Salamandre - QN08004M"
     * ]
     */
    public function searchQpv(string $code_postal, string $commune, string $adresse)
    {
        $response = $this->client->request('GET', 'https://wsa.sig.ville.gouv.fr/service/georeferenceur.json', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => ['david.bourgeois@hautsdefrance.fr', $this->sig_secret],
            'query' => [
                'type_quartier' => 'QP',
                'type_adresse' => 'AFNOR2',
                'adresse' => [
                    $adresse,
                    "$code_postal $commune",
                ],
            ],
        ]);

        $reponse = null;

        if ($response->getStatusCode() == 200) {
            $content = $response->toArray();

            if ($content['code_reponse'] == 'OUI') {
                return $content['reponses'][0];
            } else if ($content['code_reponse'] == 'NON') {
                return ['code_reponse' => 'NON'];
            } else {
                return null;
            }
        }

        return $reponse;
    }

    public function findCommunesBelges(string $q): array
    {
        $url = "https://www.odwb.be/api/records/1.0/search/?dataset=communes-belges0&q=$q&lang=fr";

        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() == 200) {
            return $response->toArray();
        }

        return [];
    }

    public function formatCommuneBelgeResponse(array $response): array
    {
        $communes = [];

        if ($response['records']) {
            foreach ($response['records'] as $key => $item) {
                $communes[$key]['nom'] = $item['fields']['name'];
                $communes[$key]['code'] = $item['fields']['nsi'];
                $communes[$key]['longitude'] = $item['geometry']['coordinates'][0];
                $communes[$key]['latitude'] = $item['geometry']['coordinates'][1];
            }
        }

        return $communes;
    }

    public function findHdfEpci(): array
    {
        $response = $this->client->request('GET', 'https://sig.ville.gouv.fr/recherche-adresses-qp-polville-v2019', [
            'body' => [
                'insee_com' => $insee_com,
                'code_postal' => $code_postal,
                'nom_commune' => $nom_commune,
                'num_adresse' => $num_adresse,
                'nom_voie' => $nom_voie,
            ],
        ]);

        $reponse = '';
        $message = '';

        if ($response->getStatusCode() == 200) {
            // Réponses possibles : UNDEF, NO_VOIE, NOK ou OK
            $content = $response->toArray();

            $reponse = $content['reponse'];
            $message = $content['message'];
        }

        return [
            'response' => $reponse,
            'message' => $message,
        ];
    }
}