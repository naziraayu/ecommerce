<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrder extends Notification
{
    use Queueable;
    protected $order;
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
            'title'   => 'New Order',
            'message' => 'New order received: Order #' . $this->order->id,
            'order_id' => $this->order->id,
            'status' => $this->order->status ?? 'pending', // Tambahkan status
            'name' => $this->order->user->name,
            'icon'    => 'fas fa-shopping-cart',
        ];
    }
}