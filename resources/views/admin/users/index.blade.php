@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('user.list users')}}</h2>
    @php
    // Data dummy untuk users
    $dummyUsers = [
        (object)[
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ],
        (object)[
            'id' => 2,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ],
        (object)[
            'id' => 3,
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com'
        ],
    ];
@endphp

<table id="table" class="table table-striped" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>{{ __('user.name') }}</th>
            <th>Email</th>
            <th>{{ __('user.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dummyUsers as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info">View</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection
