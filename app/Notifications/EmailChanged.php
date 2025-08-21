<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailChanged extends Notification
{
    use Queueable;

    protected $oldEmail;
    protected $newEmail;

    public function __construct($oldEmail, $newEmail)
    {
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Perubahan Email Akun')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Email akun Anda telah berhasil diperbarui.')
            ->line('Email lama: ' . $this->oldEmail)
            ->line('Email baru: ' . $this->newEmail)
            ->line('Jika Anda tidak melakukan perubahan ini, segera hubungi admin.');
    }
}
