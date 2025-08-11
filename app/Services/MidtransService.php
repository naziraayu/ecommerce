<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Notification;
use App\Models\Order;
use Exception;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction(Order $order)
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->transaction_id,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'item_details' => $this->buildItemDetails($order),
            ];

            $snapToken = Snap::getSnapToken($params);
            
            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function handleNotification()
    {
        try {
            $notification = new Notification();
            
            $transactionId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            
            $order = Order::where('transaction_id', $transactionId)->first();
            
            if (!$order) {
                throw new Exception('Order not found');
            }

            // Update order berdasarkan status
            $this->updateOrderStatus($order, $transactionStatus, $fraudStatus, $notification);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function updateOrderStatus(Order $order, $transactionStatus, $fraudStatus, $notification)
    {
        $order->midtrans_response = $notification->getResponse();
        $order->midtrans_transaction_id = $notification->transaction_id;

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus == 'challenge') {
                    $order->payment_status = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $order->payment_status = 'paid';
                    $order->paid_at = now();
                    $order->status = 'processing';
                }
                break;

            case 'settlement':
                $order->payment_status = 'paid';
                $order->paid_at = now();
                $order->status = 'processing';
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
    }

    private function buildItemDetails(Order $order)
    {
        $items = [];
        
        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => $item->product->id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];
        }
        
        return $items;
    }

    public function getTransactionStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return [
                'success' => true,
                'data' => $status
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}