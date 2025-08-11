<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function show(Order $order)
    {
        // Pastikan user hanya bisa akses order miliknya
        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        // Generate transaction ID jika belum ada
        if (!$order->transaction_id) {
            $order->transaction_id = 'ORDER-' . $order->id . '-' . time();
            $order->save();
        }

        // Buat snap token jika order masih pending
        $snapToken = null;
        if ($order->payment_status === 'pending') {
            $result = $this->midtransService->createTransaction($order);
            
            if ($result['success']) {
                $snapToken = $result['snap_token'];
            } else {
                return back()->with('error', 'Failed to create payment: ' . $result['message']);
            }
        }

        return view('payment.show', compact('order', 'snapToken'));
    }

    public function callback(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $result = $this->midtransService->handleNotification();
            
            if ($result['success']) {
                DB::commit();
                return response()->json(['status' => 'success']);
            } else {
                DB::rollback();
                Log::error('Midtrans callback failed: ' . $result['message']);
                return response()->json(['status' => 'error'], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Midtrans callback exception: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
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
