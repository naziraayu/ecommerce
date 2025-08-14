<?php

namespace App\Services;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use Midtrans\Transaction;
use Midtrans\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransService
{
   public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Membuat transaksi Midtrans Snap
     */
    public function createTransaction($order)
    {
        // Pakai string unik biar gak bentrok
        $orderId = 'ORDER-' . $order->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id'       => (string) $item->product_id,
                    'price'    => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name'     => $item->product->name ?? 'Produk',
                ];
            })->toArray(),
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
                'phone'      => $order->user->phone_number ?? '',
                'address'    => $order->user->address ?? '',
            ],
        ];

        // Request Snap Token ke Midtrans
        $snapToken = Snap::createTransaction($params)->token;

        // Simpan Snap Token & order_id unik Midtrans ke DB
        $order->update([
            'snap_token'              => $snapToken,
            'midtrans_transaction_id' => $orderId, // supaya nanti bisa tracking
        ]);

        return $snapToken;
    }

    /**
     * Menangani notifikasi webhook dari Midtrans
     */
    
    public function handleNotification($request = null)
    {
        try {
            $notification = new Notification();

            $orderIdFromMidtrans = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type;
            $fraudStatus = $notification->fraud_status ?? null;
            $transactionTime = $notification->transaction_time ?? now()->toDateTimeString();

            Log::info('Processing Midtrans notification', [
                'order_id' => $orderIdFromMidtrans,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType
            ]);

            // --- Cari order ---
            $order = null;

            // Coba cari berdasarkan format ORDER-{id}-{timestamp}
            if (preg_match('/ORDER-(\d+)-\d+/', $orderIdFromMidtrans, $matches)) {
                $originalOrderId = $matches[1];
                $order = Order::find($originalOrderId);
            }

            // Kalau belum ketemu, cari berdasarkan midtrans_transaction_id
            if (!$order) {
                $order = Order::where('midtrans_transaction_id', $orderIdFromMidtrans)->first();
            }

            // Kalau tetap tidak ketemu → error
            if (!$order) {
                Log::error('Order not found', ['order_id' => $orderIdFromMidtrans]);
                return [
                    'success' => false,
                    'message' => "Order tidak ditemukan untuk order_id: {$orderIdFromMidtrans}"
                ];
            }

            // --- Update data pembayaran ---
            $order->midtrans_transaction_id = $orderIdFromMidtrans;
            $order->midtrans_response = json_encode($notification);
            $order->payment_type = $paymentType;

            switch ($transactionStatus) {
                case 'capture':
                    if ($paymentType === 'credit_card') {
                        if ($fraudStatus === 'challenge') {
                            $order->payment_status = 'challenge';
                        } else {
                            $order->payment_status = 'paid';
                            $order->status = 'completed';
                            $order->paid_at = \Carbon\Carbon::parse($transactionTime);
                        }
                    }
                    break;

                case 'settlement':
                    $order->payment_status = 'paid';
                    $order->status = 'completed';
                    $order->paid_at = \Carbon\Carbon::parse($transactionTime);
                    break;

                case 'pending':
                    $order->payment_status = 'pending';
                    break;

                case 'deny':
                case 'expire':
                case 'cancel':
                    $order->payment_status = 'failed';
                    $order->status = 'cancelled';
                    break;
            }

            $order->save();

            Log::info('Order updated successfully', [
                'order_id' => $order->id,
                'payment_status' => $order->payment_status,
                'status' => $order->status
            ]);

            return [
                'success' => true,
                'message' => 'Notification processed successfully',
                'order_id' => $order->id
            ];

        } catch (Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    
}