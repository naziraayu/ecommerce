<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;


class NotificationController extends Controller
{
     /**
     * Tampilkan daftar notifikasi.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = $user->notifications()->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Tandai notifikasi sebagai dibaca (dipanggil oleh tombol View).
     */
    public function show($id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->route('notifications.index')->with('success', 'Notifikasi sudah dibaca.');
    }

    /**
     * Hapus notifikasi.
     */
    public function destroy($id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
