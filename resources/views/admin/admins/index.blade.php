@extends('layouts.admin')

@section('content')
<h2 class="mt-3">{{__('admin.list admins')}}</h2>
        
<a href="{{ route('admins.create') }}" class="btn btn-primary mb-3">{{__('admin.add')}}</a>

@php
    // Data dummy untuk admin
    $dummyAdmins = [
        (object)[
            'id' => 1,
            'name' => 'Admin One',
            'email' => 'admin1@example.com',
        ],
        (object)[
            'id' => 2,
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
        ],
        (object)[
            'id' => 3,
            'name' => 'Admin Three',
            'email' => 'admin3@example.com',
        ],
    ];
@endphp

<table id="adminTable" class="table table-striped" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>{{ __('user.name') }}</th>
            <th>Email</th>
            <th>{{ __('user.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dummyAdmins as $admin)
            <tr>
                <td>{{ $admin->id }}</td>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>
                    <a href="{{ route('admins.edit', 1) }}" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection