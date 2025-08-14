<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Exports\OrderExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->get(); // ambil semua order dengan user-nya
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function checkout()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        /** @var \App\Models\User $user */
        $cartItems = $user->carts()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;

            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak cukup untuk produk {$item->product->name}"
                    ], 400);
                }

                $totalPrice += $item->product->price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);

                // Kurangi stok produk
                $item->product->decrement('stock', $item->quantity);
            }

            // Hapus isi cart
            Cart::where('user_id', $user->id)->delete();
            // Kirim notifikasi ke semua admin & superadmin

            $order->load('user');

            $admins = \App\Models\User::whereHas('roleData', function($q) {
                $q->whereIn('name', ['admin', 'superadmin']);
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewOrder($order));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export() 
    {
        try {
            return Excel::download(new OrderExport(), 'orders_'.date('Ymd_His').'.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    public function apiIndex()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $query = Order::with('items.product', 'user')
            ->orderBy('created_at', 'desc');

        // Kalau bukan admin/superadmin, tampilkan order milik user sendiri
        if (!in_array($user->roleData->name, ['admin', 'superadmin'])) {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->get());
    }

    public function apiShow($id)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $order = Order::with('items.product', 'user')->findOrFail($id);

        // Cek akses
        if (!in_array($user->roleData->name, ['admin', 'superadmin']) && $order->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // Kirim notifikasi ke user pemilik order
        Notification::send($order->user, new OrderStatusChanged($order, $oldStatus, $request->status));

        return response()->json([
            'success' => true,
            'message' => 'Status order berhasil diubah',
            'new_status' => $order->status
        ]);
    }

}
