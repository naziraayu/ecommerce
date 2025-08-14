<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Create notification for new user registration
     */
    public static function notifyNewUserRegistered(User $newUser): void
    {
        // Notify all admins about new user
        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::createNotification(
                $admin,
                'Pengguna Baru Terdaftar',
                "Pengguna baru {$newUser->name} ({$newUser->email}) telah mendaftar",
                'user',
                [
                    'priority' => 'medium',
                    'icon' => 'fas fa-user-plus',
                    'related_id' => $newUser->id,
                    'related_type' => User::class,
                    'action_url' => route('admin.users.show', $newUser->id),
                    'metadata' => [
                        'user_email' => $newUser->email,
                        'registration_date' => $newUser->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            );
        }
    }

    /**
     * Create notification for new order
     */
    public static function notifyNewOrder(Order $order): void
    {
        // Notify all admins about new order
        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::createNotification(
                $admin,
                'Pesanan Baru Masuk',
                "Pesanan baru #{$order->id} dari {$order->user->name} senilai Rp " . number_format($order->total_amount, 0, ',', '.'),
                'order',
                [
                    'priority' => 'high',
                    'icon' => 'fas fa-shopping-cart',
                    'related_id' => $order->id,
                    'related_type' => Order::class,
                    'action_url' => route('admin.orders.show', $order->id),
                    'metadata' => [
                        'order_total' => $order->total_amount,
                        'customer_name' => $order->user->name,
                        'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                        'items_count' => $order->orderItems->count() ?? 0
                    ]
                ]
            );
        }

        // Notify customer about order confirmation
        Notification::createNotification(
            $order->user,
            'Pesanan Berhasil Dibuat',
            "Pesanan Anda #{$order->id} telah berhasil dibuat dan sedang diproses",
            'order',
            [
                'priority' => 'high',
                'icon' => 'fas fa-check-circle',
                'related_id' => $order->id,
                'related_type' => Order::class,
                'metadata' => [
                    'order_status' => $order->status,
                    'order_total' => $order->total_amount
                ]
            ]
        );
    }

    /**
     * Create notification for order status change
     */
    public static function notifyOrderStatusChanged(Order $order, string $oldStatus, string $newStatus): void
    {
        $statusMessages = [
            'pending' => 'Pesanan Anda sedang menunggu konfirmasi',
            'confirmed' => 'Pesanan Anda telah dikonfirmasi dan sedang diproses',
            'processing' => 'Pesanan Anda sedang dalam proses pengerjaan',
            'shipped' => 'Pesanan Anda telah dikirim',
            'delivered' => 'Pesanan Anda telah sampai di tujuan',
            'cancelled' => 'Pesanan Anda telah dibatalkan',
            'completed' => 'Pesanan Anda telah selesai'
        ];

        $priorityMap = [
            'confirmed' => 'high',
            'shipped' => 'high',
            'delivered' => 'high',
            'cancelled' => 'medium',
            'completed' => 'medium',
            'processing' => 'medium',
            'pending' => 'low'
        ];

        $iconMap = [
            'confirmed' => 'fas fa-check-circle',
            'processing' => 'fas fa-cog',
            'shipped' => 'fas fa-truck',
            'delivered' => 'fas fa-box-open',
            'cancelled' => 'fas fa-times-circle',
            'completed' => 'fas fa-star',
            'pending' => 'fas fa-clock'
        ];

        // Notify customer about status change
        Notification::createNotification(
            $order->user,
            'Status Pesanan Diperbarui',
            "Pesanan #{$order->id}: " . ($statusMessages[$newStatus] ?? "Status berubah menjadi {$newStatus}"),
            'order',
            [
                'priority' => $priorityMap[$newStatus] ?? 'medium',
                'icon' => $iconMap[$newStatus] ?? 'fas fa-info-circle',
                'related_id' => $order->id,
                'related_type' => Order::class,
                'metadata' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'status_changed_at' => now()->format('Y-m-d H:i:s')
                ]
            ]
        );

        // Notify admins for important status changes
        if (in_array($newStatus, ['cancelled', 'delivered', 'completed'])) {
            $admins = User::admins()->get();
            
            foreach ($admins as $admin) {
                Notification::createNotification(
                    $admin,
                    'Status Pesanan Diperbarui',
                    "Pesanan #{$order->id} dari {$order->user->name} telah {$newStatus}",
                    'order',
                    [
                        'priority' => 'medium',
                        'icon' => $iconMap[$newStatus] ?? 'fas fa-info-circle',
                        'related_id' => $order->id,
                        'related_type' => Order::class,
                        'action_url' => route('admin.orders.show', $order->id),
                        'metadata' => [
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'customer_name' => $order->user->name
                        ]
                    ]
                );
            }
        }
    }

    /**
     * Create notification for new product added
     */
    public static function notifyNewProduct(Product $product): void
    {
        // Notify admins about new product
        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::createNotification(
                $admin,
                'Produk Baru Ditambahkan',
                "Produk baru '{$product->name}' telah ditambahkan ke catalog",
                'product',
                [
                    'priority' => 'low',
                    'icon' => 'fas fa-box',
                    'related_id' => $product->id,
                    'related_type' => Product::class,
                    'action_url' => route('admin.products.show', $product->id),
                    'metadata' => [
                        'product_name' => $product->name,
                        'product_price' => $product->price,
                        'category' => $product->category->name ?? null
                    ]
                ]
            );
        }
    }

    /**
     * Create notification for low stock product
     */
    public static function notifyLowStock(Product $product): void
    {
        // Notify admins about low stock
        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::createNotification(
                $admin,
                'Stok Produk Menipis',
                "Produk '{$product->name}' memiliki stok tersisa {$product->stock} unit",
                'product',
                [
                    'priority' => 'high',
                    'icon' => 'fas fa-exclamation-triangle',
                    'related_id' => $product->id,
                    'related_type' => Product::class,
                    'action_url' => route('admin.products.edit', $product->id),
                    'metadata' => [
                        'product_name' => $product->name,
                        'current_stock' => $product->stock,
                        'minimum_stock' => $product->minimum_stock ?? 10
                    ]
                ]
            );
        }
    }

    /**
     * Create notification for payment received
     */
    public static function notifyPaymentReceived(Order $order, array $paymentData = []): void
    {
        // Notify admins about payment
        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::createNotification(
                $admin,
                'Pembayaran Diterima',
                "Pembayaran untuk pesanan #{$order->id} telah diterima sebesar Rp " . number_format($order->total_amount, 0, ',', '.'),
                'payment',
                [
                    'priority' => 'medium',
                    'icon' => 'fas fa-credit-card',
                    'related_id' => $order->id,
                    'related_type' => Order::class,
                    'action_url' => route('admin.orders.show', $order->id),
                    'metadata' => array_merge([
                        'order_total' => $order->total_amount,
                        'customer_name' => $order->user->name,
                        'payment_date' => now()->format('Y-m-d H:i:s')
                    ], $paymentData)
                ]
            );
        }

        // Notify customer about payment confirmation
        Notification::createNotification(
            $order->user,
            'Pembayaran Berhasil',
            "Pembayaran untuk pesanan #{$order->id} telah berhasil diproses",
            'payment',
            [
                'priority' => 'high',
                'icon' => 'fas fa-check-circle',
                'related_id' => $order->id,
                'related_type' => Order::class,
                'metadata' => [
                    'payment_amount' => $order->total_amount,
                    'payment_status' => 'success'
                ]
            ]
        );
    }

    /**
     * Create custom notification
     */
    public static function createCustomNotification(
        User $user,
        string $title,
        string $message,
        string $category = 'system',
        array $options = []
    ): Notification {
        return Notification::createNotification($user, $title, $message, $category, $options);
    }

    /**
     * Broadcast notification to all admins
     */
    public static function broadcastToAdmins(
        string $title,
        string $message,
        string $category = 'system',
        array $options = []
    ): int {
        $admins = User::admins()->get();
        $count = 0;

        foreach ($admins as $admin) {
            self::createCustomNotification($admin, $title, $message, $category, $options);
            $count++;
        }

        return $count;
    }

    /**
     * Broadcast notification to all users
     */
    public static function broadcastToAllUsers(
        string $title,
        string $message,
        string $category = 'system',
        array $options = []
    ): int {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            self::createCustomNotification($user, $title, $message, $category, $options);
            $count++;
        }

        return $count;
    }
}