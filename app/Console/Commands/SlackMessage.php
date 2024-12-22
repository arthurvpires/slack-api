<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Services\SlackService;

class SlackMessage extends Command
{
    protected $signature = 'send:slack-message {--channel=} {--message=}';
    protected $description = 'Sends a message to an specific slack channel';

    protected $slackService;

    public function __construct()
    {
        parent::__construct();
        $this->slackService = new SlackService();
    }

    public function handle()
    {
        $channelName = $this->option('channel') ?? 'integração-api';
        $message = $this->option('message') ?? 'Mensagem enviada via Slack API';
        $progressBar = $this->output->createProgressBar(100);

        try {
            $progressBar->start();
            $channels = $this->slackService->listChannels();
            $channel = collect($channels)->firstWhere('name', $channelName);

            if (!$channel) {
                $this->error("Channel not found: {$channelName}");
                return;
            }

            $channelId = $channel['id'];
            $this->slackService->sendMessage($channelId, $message);
            
            $progressBar->finish();
            $this->newLine();

            $this->info("Message sent!");
            $this->newLine();
            $this->info("Channel: {$channelName}");
            $this->info("Message: {$message}");

        } catch (Exception $e) {
            $this->error("Failed to send message: " . $e->getMessage());
        }
    }
}
