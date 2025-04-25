<?php

namespace App\Services;

use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Captcha
{
    public function __construct(
        private string $client_id,
        private int $client_code,
        private string $secret,
        private string $url,
        private HttpClientInterface $client
    )
    {}

    /**
     * Retourne le challenge ID du captcha
     *
     * @return string|false
     */
    public function create(): string|false
    {
        $response = $this->client->request('POST', $this->url . '/public/backend/api/v2/captcha', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->client_id, $this->secret],
            'query' => [
                "type" => "IMAGE",
                "locale" => "FR",
                'theme' => 'RANDOM',
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            $responseData = json_decode($response->getContent());

            return $responseData->id;
        }

        return false;
    }

    public function getImageUrl(string $challeng_id): string
    {
        return $this->url . '/public/frontend/api/v2/captcha/' . $challeng_id . '.png';
    }

    /**
     * Vérifie si un captcha est valide
     */
    public function check(string $captcha_challenge_id, string $captcha_answer): bool
    {
        $response = $this->client->request('POST', $this->url . '/public/backend/api/v2/captcha/' . $captcha_challenge_id . '/checkAnswer', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->client_id, $this->secret],
            'json' => [
                'answer' => $captcha_answer,
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            $responseData = json_decode($response->getContent());

            return $responseData->result == 'SUCCESS';
        }

        return false;
    }

    public function getStatistiques(string $start = null, string $end = null): \stdClass|false
    {
        $url = $this->url . '/public/backend/api/v2/captcha/stats';
        if ($start && $end) {
            $url .= '?startDate=' . $start . '&endDate=' . $end;
        }

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->client_id, $this->secret],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            return json_decode($response->getContent());
        }

        return false;
    }


    /**
     * @param string $userAgent
     * @param string $userUrl
     * @return string|false
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getAntibotInfo(string $userAgent, string $userUrl)
    {
        $response = $this->client->request('POST', $this->url . '/public/backend/api/v3/captcha/sendAntibotInfo', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->client_id, $this->secret],
            'json' => [
                "antibotId" => "",
                "headers" => "Host: $userUrl\nDNT: 1\nContent-Type: application/json\nuser-agent: $userAgent",
                "bodySize" => 0,
                "ip" => "91.230.1.2",
                "method" => "GET",
                "path" => "/captcha",
                "port" => 443,
                "protocol" => "HTTPS",
                "timestamp" => 948118200000,
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            return json_decode($response->getContent());
        }

        return false;
    }

    /**
     * @param string $token
     * @return bool
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function checkAntibotToken(string $token)
    {
        if ($token == 'Invalid response.' || $token == 'Blacklisted end-user.'  || $token == 'Suspicious end-user.' ||
            $token == 'Unable to calculate the end-user invisible captcha status.') {
            return false;
        }

        $response = $this->client->request('POST', $this->url . '/public/backend/api/v3/captcha/check', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->client_id, $this->secret],
            'json' => [
                "antibotToken" => $token,
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            $responseData = json_decode($response->getContent());

            return $responseData->result == 'SUCCESS';
        }

        return false;
    }
}