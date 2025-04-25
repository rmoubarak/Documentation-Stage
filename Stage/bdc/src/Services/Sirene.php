<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * https://api.insee.fr/catalogue/site/themes/wso2/subthemes/insee/pages/item-info.jag?name=Sirene&version=V3&provider=insee#/
 */
class Sirene
{
    public function __construct(
        private string $key,
        private string $secret,
        private HttpClientInterface $client,
        private RequestStack $requestStack
    )
    {
    }

    /**
     * Mise en session du token
     *
     * @return bool|array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function registerAccessToken(): bool
    {
        $response = $this->client->request('POST', 'https://api.insee.fr/token', [
            'auth_basic' => [$this->key, $this->secret],
            'body' => [
                "grant_type" => "client_credentials",
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            // Sauvegarde du token et de sa limite de validité
            $session = $this->requestStack->getSession();
            $now = new \DateTime();

            $session->set('sirene_token', $response->toArray()['access_token']);
            $session->set('sirene_token_limit', $now->modify('+ ' . $response->toArray()['expires_in'] . 'seconds'));

            return true;
        }

        return false;
    }

    /**
     * Vérifie si le token est toujours valide
     *
     * @return bool
     */
    private function isTokenValid(): bool
    {
        $now = new \DateTime();
        $validity_limit = $this->requestStack->getSession()->get('sirene_token_limit');

        return $validity_limit && $validity_limit->getTimestamp() > $now->getTimestamp();
    }

    /**
     * Renvoie un token (en session ou via appel API)
     *
     * @return false|mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getToken(): string|false
    {
        if ($this->isTokenValid() || $this->registerAccessToken()) {
            return $this->requestStack->getSession()->get('sirene_token');
        }

        return false;
    }

    public function searchEtablissement(string $siren = '', string $denomination = '', bool $hdf = true, int $limite = 20): array|false
    {
        $token = $this->getToken();

        if (!$token) {
            return false;
        }

        $q = '';
        if ($siren) {
            $q = 'siren:' . trim($siren);
        }

        $q2 = '';
        if ($denomination) {
            $q2 = 'denominationUniteLegale:"' . trim($denomination) . '"~2';
        }

        $url = '?q=' . $q . ($q && $q2 ? ' AND ' . $q2 : $q2);

        // Limite aux établissements des HDF
        //codePostalEtablissement
        if ($hdf) {
            $url .= ' AND (codePostalEtablissement:02* OR codePostalEtablissement:59* OR codePostalEtablissement:60* OR codePostalEtablissement:62* OR codePostalEtablissement:80*)';
        }

        $url .= '&nombre=' . $limite;

        $response = $this->client->request('GET', 'https://api.insee.fr/entreprises/sirene/V3/siret' . $url, [
            'auth_bearer' => $token,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            //dd($response->toArray());

            return $response->toArray();
        }

        return false;
    }
}
