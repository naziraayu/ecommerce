<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\User;

class NewUserRegistered extends Notification 
{
    use Queueable;
    public $user;
    protected $ccEmails;

    public function __construct(User $user, $ccEmails = [])
    {
        $this->user = $user;
        $this->ccEmails = $ccEmails;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Pengguna Baru',
            'user_id' => $this->user->id,
            'name' => $this->user->name,
            'message' => "User baru dengan nama {$this->user->name} telah mendaftar.",
            'icon'    => 'fas fa-user-plus',
        ];
    }
}