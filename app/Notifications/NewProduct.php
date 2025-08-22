<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewProduct extends Notification
{
    use Queueable;
    
    protected $product;
    protected $ccEmails;

    public function __construct($product, $ccEmails = [])
    {
        $this->product = $product;
        $this->ccEmails = $ccEmails;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Hanya database, tidak email
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Produk Baru', // Tambahkan title
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'message' => 'Produk baru ditambahkan: ' . $this->product->name,
            'icon'    => 'fas fa-box', // Tambahkan icon
        ];
    }

}