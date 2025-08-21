<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['roleData', 'orders'])->findOrFail($id); // pastikan relasi roleData & orders ada
        return view('admin.users.show', compact('user'));
    }

    public function export() 
    {
        return Excel::download(new UserExport, 'daftar_user.xlsx');
    }
}
