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
        return view('admin.admins.index');
    }

    public function edit()
    {
        return view('admin.admins.edit');
    }

    public function update()
    {
        return redirect()->route('admins.index')->with('success', 'Admin updated successfully.');
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admins.index')->with('success', 'Admins created successfully.');
    }
}
