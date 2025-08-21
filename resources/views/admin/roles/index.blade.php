@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('role.title') }}</h3>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">{{ __('role.create') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="roles-table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>{{ __('role.role_name') }}</th>
                        <th>{{ __('role.permissions') }}</th>
                        <th>{{ __('role.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $index => $role)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                @if(!empty($role->permissions))
                                    <ul class="mb-0 ps-3">
                                        @foreach($role->permissions as $permission)
                                            <li>{{ $permission }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">{{ __('role.no_permissions') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">{{ __('role.edit') }}</a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteRole({{ $role->id }})">
                                    {{ __('role.delete') }}
                                </button>

                                <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
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
        if ($.fn.DataTable.isDataTable('#roles-table')) {
            $('#roles-table').DataTable().destroy();
        }

        let langUrl = (lang === 'id') 
            ? "/assets/indonesia.json" 
            : "/assets/english.json";

        table = $('#roles-table').DataTable({
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
<script>
    function deleteRole(id) {
        Swal.fire({
            title: "{{ __('role.confirm_delete') ?? 'Yakin?' }}",
            text: "{{ __('role.confirm_delete') ?? 'Role ini akan dihapus permanen!' }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: "{{ __('role.yes_delete') ?? 'Ya, hapus!' }}",
            cancelButtonText: "{{ __('role.cancel') ?? 'Batal' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
        // Notifikasi sukses dari session (opsional)
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    }
</script>

@endpush
