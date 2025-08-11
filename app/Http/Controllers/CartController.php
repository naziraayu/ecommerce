<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function jsonResponse($success, $message, $data = null, $status = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ], $status);
    }

    public function index()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->jsonResponse(false, 'User not authenticated', null, 401);
        }

        /** @var \App\Models\User $user */
        $cart = $user->carts()->with('product')->get();

        return $this->jsonResponse(true, 'Cart retrieved successfully', $cart);
    }


    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->jsonResponse(false, 'User not authenticated', null, 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->product_id);

            if ($product->stock < $request->quantity) {
                return $this->jsonResponse(false, 'Stock tidak mencukupi', null, 400);
            }

            $product->decrement('stock', $request->quantity);

            $cart = Cart::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $product->id],
                ['quantity' => DB::raw('quantity + ' . $request->quantity)]
            );

            DB::commit();
            return $this->jsonResponse(true, 'Produk berhasil ditambahkan ke cart', $cart);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function update(Request $request, $cartId)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->jsonResponse(false, 'User not authenticated', null, 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $cart = Cart::where('id', $cartId)->where('user_id', $user->id)->firstOrFail();
            $product = $cart->product;

            $selisih = $request->quantity - $cart->quantity;

            if ($selisih > 0) {
                if ($product->stock < $selisih) {
                    return $this->jsonResponse(false, 'Stock tidak mencukupi', null, 400);
                }
                $product->decrement('stock', $selisih);
            } elseif ($selisih < 0) {
                $product->increment('stock', abs($selisih));
            }

            $cart->update(['quantity' => $request->quantity]);

            DB::commit();
            return $this->jsonResponse(true, 'Quantity cart diperbarui', $cart);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function destroy($cartId)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->jsonResponse(false, 'User not authenticated', null, 401);
        }

        DB::beginTransaction();
        try {
            $cart = Cart::where('id', $cartId)->where('user_id', $user->id)->firstOrFail();
            $product = $cart->product;

            $product->increment('stock', $cart->quantity);
            $cart->delete();

            DB::commit();
            return $this->jsonResponse(true, 'Produk dihapus dari cart');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
