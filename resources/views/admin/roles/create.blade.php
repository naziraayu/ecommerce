@extends('layouts.admin')

@section('content')
<div class="card mt-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Buat Role Baru</h5>
        <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">Kembali</a>
    </div>
    <div class="card-body">

        {{-- Error alert --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nama Role</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: superadmin" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="permissions" class="form-label">Permissions</label>
                <select name="permissions[]" id="permissions" class="form-select select2" multiple required>
                    @foreach ($permissions as $permission)
                        <option value="{{ $permission }}" {{ in_array($permission, old('permissions', [])) ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $permission)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Simpan Role</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Pilih permission...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
