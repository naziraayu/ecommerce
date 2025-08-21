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

    /**
     * Create a new notification instance.
     */
    public function __construct($product, $ccEmails = [])
    {
        $this->product = $product;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Get the notification's delivery channels.


     */
    public function via(object $notifiable): array
    {
        return ['database']; // Hanya database, tidak email
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'message' => 'Produk baru ditambahkan: ' . $this->product->name,
        ];
    }

    // Method toMail() bisa dihapus atau di-comment karena tidak digunakan
    /*
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('New Product Added: ' . $this->product->name)
            ->line('A new product has been added: ' . $this->product->name)
            ->line('Check out the latest product now!')
            ->salutation('Admin, Ecommerce');

        if (!empty($this->ccEmails)) {
            $mail->cc($this->ccEmails);
        }

        return $mail;
    }
    */
}