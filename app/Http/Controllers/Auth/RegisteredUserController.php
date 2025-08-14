<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Notifications\NewUserRegistered;
use Illuminate\Support\Facades\Notification;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            // 'role_id' => 'nullable|integer',
        ]);

        // Ambil role admin dari tabel roles
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'role_id'      => $adminRole->id,
        ]);

        // Ambil semua admin lain untuk dikirimi notifikasi
        $otherAdmins = User::where('role_id', $adminRole->id)
            ->where('id', '!=', $user->id)
            ->get();

        // Kirim notifikasi ke admin lain
        if ($otherAdmins->isNotEmpty()) {
            Notification::send($otherAdmins, new NewUserRegistered($user));
        }

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Arahkan ke halaman verifikasi email
        return redirect()->route('verification.notice');
    }
}
