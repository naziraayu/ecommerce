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
    $users = User::where('id', '!=', Auth::id())
        ->whereHas('roleData', function($query) {
            $query->where('name', 'admin');
        })
        ->get();

    // Dropdown role tetap bisa pilih admin atau superadmin kalau mau upgrade
    $roles = Role::whereIn('name', ['admin', 'superadmin'])->get();

    return view('admin.admins.index', compact('users', 'roles'));
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

        // Role default = admin
        $adminRole = Role::where('name', 'admin')->firstOrFail();

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
        $roles = Role::all(); // supaya dropdown muncul di edit
        return view('admin.admins.edit', compact('admin', 'roles'));
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
            'role_id' => 'required|exists:roles,id',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone_number = $validated['phone_number'] ?? null;
        $admin->address = $validated['address'] ?? null;
        $admin->role_id = $validated['role_id']; // update role

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
