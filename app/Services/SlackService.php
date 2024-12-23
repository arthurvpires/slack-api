<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class SlackService
{
    protected $httpClient;
    protected $token;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->token = env('SLACK_BOT_USER_OAUTH_TOKEN');
    }

    public function sendMessage(string $userId, string $message): ?array
    {
        $reqOptions = [
            RequestOptions::HEADERS => $this->getHeaders(),
            RequestOptions::JSON => [
                'channel' => $userId,
                'text' => $message,
            ],
        ];

        $response = $this->httpClient->post(
            env('SLACK_BASE_URL') . 'chat.postMessage',
            $reqOptions
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (empty($data['ok'])) {
            throw new \Exception($data['error'] ?? 'Unknown error');
        }

        return $data;
    }

    public function getUserIdByEmail(string $email): string
    {
        $response = $this->httpClient->get(
            env('SLACK_BASE_URL') . 'users.lookupByEmail',
            [
                RequestOptions::HEADERS => $this->getHeaders(),
                RequestOptions::QUERY => ['email' => $email]
            ]
        );

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['ok'] ? $data['user']['id'] : null;
    }

    public function listChannels(): array
    {
        $response = $this->httpClient->get(
            env('SLACK_BASE_URL') . 'conversations.list',
            ['headers' => $this->getHeaders()]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (empty($data['ok'])) {
            throw new \Exception($data['error'] ?? 'Unknown error');
        }

        return $data['channels'];
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Content-Type' => 'application/json',
        ];
    }

}
