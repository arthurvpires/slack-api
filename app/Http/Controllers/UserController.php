<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\SlackService;

class UserController extends Controller
{
    public function index(SlackService $slackService, Request $req)
    {
        $user = $req->user();
        $slackUserId = $slackService->getUserIdByEmail($user->email);

        if (!session()->has('has_received_login_notification')) {
            $slackService->sendMessage($slackUserId, "A new login was detected in your Slack account.");
            session()->put('has_received_login_notification', true);
        }

        return Inertia::render('Dashboard');
    }
}
