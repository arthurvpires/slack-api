<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Services\SlackService;

class UserController extends Controller
{
    public function index()
    {
        $this->sendSlackMessage(auth()->user());
        return Inertia::render('Dashboard');
    }

    private function sendSlackMessage(User $user): void
    {
        $slackService = app(SlackService::class);
        $slackUserId = $slackService->getUserIdByEmail($user->email);

        if ($user->recieve_slack_notifications && !session()->has('has_received_login_notification')) {
            $slackService->sendMessage($slackUserId, "A new login was detected in your Slack account.");
            session()->put('has_received_login_notification', true);
        }
    }
}
