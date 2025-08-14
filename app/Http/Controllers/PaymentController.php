<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        // Kalau order belum ada snap_token, generate dulu
        if (!$order->snap_token) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = false;
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . $order->id . '-' . time(), // Tambahkan timestamp untuk uniqueness
                    'gross_amount' => (int)$order->total_price, // Cast ke integer
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $order->snap_token = $snapToken;
            $order->midtrans_transaction_id = $params['transaction_details']['order_id']; // Simpan transaction ID
            $order->save();
        } else {
            $snapToken = $order->snap_token;
        }

        return view('admin.payment.show', compact('order', 'snapToken'));
    }

    public function callback(Request $request)
    {
        try {
            Log::info('Midtrans callback received', $request->all()); // Log untuk debugging
            
            DB::beginTransaction();
            
            $result = $this->midtransService->handleNotification($request);
            
            if ($result['success']) {
                DB::commit();
                Log::info('Midtrans callback processed successfully', ['order_id' => $result['order_id'] ?? null]);
                return response()->json(['status' => 'success']);
            } else {
                DB::rollback();
                Log::error('Midtrans callback failed: ' . $result['message']);
                return response()->json(['status' => 'error', 'message' => $result['message']], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Midtrans callback exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    public function success(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        return view('payment.success', compact('order'));
    }

    public function failed(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        return view('payment.failed', compact('order'));
    }
}