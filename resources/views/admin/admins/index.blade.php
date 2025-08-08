@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h3 class="mt-3">{{ __('Management Admin') }}</h3>
    <a href="{{ route('admins.create') }}" class="btn btn-primary">Tambah Admin</a>
</div>
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <table id="adminTable" class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>{{ __('user.name') }}</th>
                    <th>Email</th>
                    <th>{{ __('user.role') }}</th>
                    <th>{{ __('user.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->roleData->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admins.edit', $admin->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#adminTable').DataTable({
            language: {
                url: "{{ asset(App::getLocale() === 'id' ? 'assets/indonesia.json' : 'assets/english.json') }}"
            }
        });
    });
</script>
@endpush
