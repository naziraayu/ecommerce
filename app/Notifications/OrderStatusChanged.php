<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public $order;
    protected $ccEmails;
   
    public function __construct($order, $ccEmails = [])
    {
        $this->order = $order;
        $this->ccEmails = $ccEmails;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Status Order Berubah',
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => "Order #{$this->order->id} has been updated to {$this->order->status}.",
            'icon'    => 'fas fa-clipboard-check',
        ];
    }
}