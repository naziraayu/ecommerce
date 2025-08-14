<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\NewUserRegistered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class AuthenticationController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'role_id' => 'nullable|integer',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'role_id' => $request->role_id,
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }


    public function loginForm()
    {
        return view('auth.login');
    }

    public function loginApi(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $user->api_token = Str::random(60);
            /** @var \App\Models\User $user */
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'token' => $user->api_token,
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah',
        ], 401);
    }

    public function registerApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        // Ambil role user (case-insensitive)
        $userRole = Role::whereRaw('LOWER(name) = ?', ['user'])->firstOrFail();

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'role_id' => $userRole->id,
        ]);

        // Ambil id role admin & superadmin (case-insensitive)
        $adminRoleId = Role::whereRaw('LOWER(name) = ?', ['admin'])->value('id');
        $superAdminRoleId = Role::whereRaw('LOWER(name) = ?', ['superadmin'])->value('id');

        // Gabungkan role yang ditemukan
        $roleIds = array_filter([$adminRoleId, $superAdminRoleId]);

        if (!empty($roleIds)) {
            // Ambil semua user dengan role admin/superadmin
            $recipients = User::whereIn('role_id', $roleIds)->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new NewUserRegistered($user));
            }
        }

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user
        ]);
    }


    public function login(Request $request)
    {
        return redirect('/dashboard')->with('success', 'Login as admin successful.');
    }

    public function logout(Request $request)
    {
        return to_route('login')->with('success', 'You have logged out successfully!');
    }

    public function settingView()
    {
        return view('admin.settings.index');
    }
    
    public function emailChange(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        /** @var \App\Models\User $user */

        $user = Auth::user();
        $user->email = $request->email;
        $user->save();

        return redirect()->route('settings.index')->with('success', 'Email berhasil diperbarui.');
    }
}
