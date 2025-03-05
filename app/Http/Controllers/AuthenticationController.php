<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        return redirect('/dashboard')->with('success', 'Registration successful. Please log in.');
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

    public function settingView()
    {
        return view('admin.settings.index');
    }
    
    public function emailChange(Request $request)
    {
        return redirect('/dashboard')->with('success', 'Email has already change');
    }
}
