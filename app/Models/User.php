<?php

namespace App\Models;

use App\Services\SlackService;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'slack_id',
        'recieve_slack_notifications',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getSlackUserId(): string
    {
        if (!empty($this->slack_id)) {
            return $this->slack_id;
        }

        $service = app(SlackService::class);
        $slackId = $service->getUserIdByEmail($this->email);

        $this->setSlackId($service->getUserIdByEmail($this->email));

        return $slackId;
    }

    public function setSlackId(string  $slackId): void
    {
        $this->slack_id = $slackId;
        $this->save();
    }
}
