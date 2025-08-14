<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        
        $query = $user->notifications();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Search dalam data notifikasi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('data', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Pertahankan query parameters untuk pagination
        $notifications->appends($request->all());

        return view('admin.notifications.index', compact('notifications'));
    }

    public function show($id): RedirectResponse
    { 
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);
        
        // Tandai sebagai dibaca jika belum dibaca
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->route('notifications.index')
                        ->with('success', 'Notifikasi telah dibaca');
    }

    public function markAsRead($id): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca',
            'unread_count' => $user->fresh()->unreadNotifications()->count()
        ]);
    }

    public function markAllAsRead(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $updated = $user->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->back()
                        ->with('success', "Berhasil menandai {$updated} notifikasi sebagai dibaca");
    }

    public function destroy($id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()
                        ->with('success', 'Notifikasi berhasil dihapus');
    }

    public function getBellData(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $unreadCount = $user->unreadNotifications()->count();
            $recentNotifications = $user->notifications()
                                       ->latest()
                                       ->limit(5)
                                       ->get();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'recent_notifications' => $recentNotifications->map(function($notification) {
                    // Pastikan data tidak null
                    $data = is_array($notification->data) ? $notification->data : [];
                    
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'data' => $data,
                        'created_at' => $notification->created_at->diffForHumans(),
                        'is_read' => !is_null($notification->read_at),
                        'url' => route('notifications.show', $notification->id)
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading notifications',
                'unread_count' => 0,
                'recent_notifications' => []
            ]);
        }
    }

    public function redirect($id)
{
     /** @var User $user */
    $user = Auth::user();

    $notification = $user->notifications()->findOrFail($id);


    // Tandai dibaca
    $notification->markAsRead();

    switch ($notification->type) {
        case 'App\Notifications\NewProduct':
            return redirect()->route('products.show', $notification->data['product_id']);
        
        case 'App\Notifications\NewOrder':
            return redirect()->route('orders.show', $notification->data['order_id']);
        
        case 'App\Notifications\NewUserRegistered':
            return redirect()->route('users.show', $notification->data['user_id']);
        
        case 'App\Notifications\OrderStatusChanged':
            return redirect()->route('orders.show', $notification->data['order_id']);
        
        default:
            return redirect()->route('notifications.index');
    }
}

}