<?php

namespace App\Services;

use GuzzleHttp\Client;

class SlackService
{
    protected $client;
    protected $token;

    public function __construct()
    {
        $this->client = new Client();
        $this->token = env('SLACK_BOT_USER_OAUTH_TOKEN');
    }

    public function sendMessage(string $userId, string $message): array
    {
        try {
            $response = $this->client->post('https://slack.com/api/chat.postMessage', [
                'headers' => [
                    'Authorization' => "Bearer {$this->token}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'channel' => $userId,
                    'text' => $message,
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (empty($data['ok'])) {
                throw new \Exception($data['error'] ?? 'Unknown error');
            }

            return $data;
        } catch (\Exception $e) {
            throw new \Exception('Failed to send direct message: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getUserIdByEmail(string $email): string
    {
        $response = $this->client->get('https://slack.com/api/users.lookupByEmail', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'query' => [
                'email' => $email,
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['ok'] === true ? $data['user']['id'] : null;

    }

    public function listChannels(): array
    {
        try {
            $response = $this->client->get('https://slack.com/api/conversations.list', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['ok'])) {
                throw new \Exception($data['error'] ?? 'Unknown error');
            }

            return $data['channels'];
        } catch (\Exception $e) {
            throw new \Exception('Failed to list channels: ' . $e->getMessage(), 0, $e);
        }
    }

}
