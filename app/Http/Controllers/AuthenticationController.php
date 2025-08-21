<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ConfirmEmailChange;
use Illuminate\Support\Facades\DB;
use App\Notifications\EmailChanged;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function login(Request $request)
    {
        return redirect('/dashboard')->with('success', 'Login as admin successful.');
    }

    public function logout(Request $request)
    {
        return to_route('login')->with('success', 'You have logged out successfully!');
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

    public function settingView()
    {
        return view('admin.settings.index');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'old_email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Auth::user();

        if ($request->old_email !== $user->email) {
            return back()->with('error', 'Email lama tidak sesuai dengan akun Anda.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah.');
        }

        // Kalau valid → kasih flag supaya view bisa munculin field email baru
        return redirect()->route('settings.index')->with('verified_old', true);
    }

    public function emailChange(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $oldEmail = $user->email;

        $user->email = $request->email;
        $user->save();

        // Kirim notifikasi ke email baru
        $user->notify(new EmailChanged($oldEmail, $user->email));

        return redirect()->route('settings.index')
            ->with('success', 'Email berhasil diperbarui. Verifikasi telah dikirim ke email baru Anda.');
    }

    public function sendVerification(Request $request)
{
    $request->validate([
        'new_email' => 'required|email|unique:users,email',
    ]);

    /** @var \App\Models\User $user */

    $user = Auth::user();
    $newEmail = $request->new_email;

    // Simpan email baru ke pending_email
    $user->pending_email = $newEmail;
    $user->save();

    // Generate signed verification URL (valid 1 jam)
    $verificationUrl = URL::temporarySignedRoute(
        'settings.confirmEmailChange',
        now()->addHour(),
        ['user' => $user->id]
    );

    // Kirim Mailable
    Mail::to($newEmail)->send(new ConfirmEmailChange($verificationUrl));

    return redirect()->route('settings.index')
        ->with('success', 'Email verifikasi sudah dikirim ke ' . $newEmail);
}

    public function confirmEmailChange(Request $request, User $user)
    {
        // validasi link signed
        if (! $request->hasValidSignature()) {
            return redirect()->route('settings.index')
                ->with('error', 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        if (!$user->pending_email) {
            return redirect()->route('settings.index')
                ->with('error', 'Tidak ada perubahan email yang menunggu.');
        }

        $user->email = $user->pending_email;
        $user->pending_email = null;
        $user->save();

        return redirect()->route('settings.index')
            ->with('success', 'Email berhasil diperbarui!');
    }

    // 🔹 API 1: Request Reset Code untuk buyer (role user)
    public function requestResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)
                    ->whereHas('roleData', function ($q) {
                        $q->whereRaw('LOWER(name) = ?', ['user']); // hanya role user
                    })
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Email tidak terdaftar sebagai user.'], 404);
        }

        // generate kode OTP 6 digit
        $code = rand(100000, 999999);

        // simpan / update ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $code,
                'created_at' => now()
            ]
        );

        // kirim email
        Mail::raw("Kode reset password Anda adalah: {$code}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Reset Password Buyer');
        });

        return response()->json(['message' => 'Kode reset password sudah dikirim ke email.']);
    }

    // 🔹 API 2: Reset Password dengan Code
    public function resetPasswordWithCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
                    ->where('token', $request->code)
                    ->first();

        if (!$reset) {
            return response()->json(['error' => 'Kode tidak valid.'], 400);
        }

        $user = User::where('email', $reset->email)
                    ->whereHas('roleData', function ($q) {
                        $q->whereRaw('LOWER(name) = ?', ['user']); // hanya role user
                    })
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan.'], 404);
        }

        // update password
        $user->password = Hash::make($request->password);
        $user->save();

        // hapus kode setelah dipakai
        DB::table('password_reset_tokens')->where('email', $reset->email)->delete();

        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }
}
