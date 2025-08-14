@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h3 class="mt-3">{{ __('admin.management') }}</h3>
    <a href="{{ route('admins.create') }}" class="btn btn-primary">{{ __('admin.add') }}</a>
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
                @foreach($users as $admin) {{-- ganti $admins ke $users biar sesuai controller --}}
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            <form action="{{ route('admins.update', $admin->id) }}" method="POST" id="roleForm-{{ $admin->id }}">
                                @csrf
                                @method('PUT')
                                <select name="role_id" class="form-select form-select-sm" onchange="document.getElementById('roleForm-{{ $admin->id }}').submit()">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $admin->role_id == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- hidden inputs untuk data lain supaya update() nggak error --}}
                                <input type="hidden" name="name" value="{{ $admin->name }}">
                                <input type="hidden" name="email" value="{{ $admin->email }}">
                                <input type="hidden" name="phone_number" value="{{ $admin->phone_number }}">
                                <input type="hidden" name="address" value="{{ $admin->address }}">
                            </form>
                        </td>
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
    let table;

    function initDataTable(lang) {
        // kalau table sudah ada, destroy dulu
        if ($.fn.DataTable.isDataTable('#adminTable')) {
            $('#adminTable').DataTable().destroy();
        }

        let langUrl = (lang === 'id') 
            ? "{{ secure_asset('assets/indonesia.json') }}" 
            : "{{ secure_asset('assets/english.json') }}";

        table = $('#adminTable').DataTable({
            processing: true,
            serverSide: false,
            language: {
                url: langUrl
            }
        });
    }

    $(document).ready(function () {
        // inisialisasi pertama sesuai locale Laravel
        let lang = "{{ app()->getLocale() }}";
        initDataTable(lang);

        // contoh: kalau user ganti bahasa (misal pakai select)
        $('#languageSelect').change(function() {
            let newLang = $(this).val();
            initDataTable(newLang);
        });
    });
</script>

@endpush
