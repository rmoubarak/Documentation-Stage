<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * https://data.education.gouv.fr/api/v2/console
 */
class EducationNationale
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function findColleges(
        string $q = '',
        string $type = 'Collège',
        string $statut = 'PUBLIC',
        int $limite = 20,
        int $offset = 0
    ): array|false
    {

        $url = "?where=type_etablissement%3D%22$type%22%20AND%20code_region%3D32&order_by=code_commune&limit=$limite&offset=$offset&statut_public_prive%3D%22$statut%22&lang=fr&timezone=UTC";

        $response = $this->client->request('GET', 'https://data.education.gouv.fr/api/explore/v2.0/catalog/datasets/fr-en-annuaire-education/records' . $url);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            return $response->toArray();
        }

        return false;
    }
}
