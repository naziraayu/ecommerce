<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::whereHas('roleData', function ($query) {
            $query->where('name', '!=', 'user'); // hanya admin (bukan user biasa)
        })->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'user')->get();
        return view('admin.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        // Ambil role admin (pastikan role 'admin' ada di tabel roles)
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            return redirect()->back()->with('error', 'Role admin tidak ditemukan.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'role_id' => $adminRole->id,
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone_number = $validated['phone_number'] ?? null;
        $admin->address = $validated['address'] ?? null;

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Admin updated successfully.');
    }


    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Admin deleted successfully.');
    }

}
