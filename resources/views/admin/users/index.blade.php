@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('user.list users') }}</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table id="userTable" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>{{ __('user.name') }}</th>
                        <th>Email</th>
                        <th>{{ __('user.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <a href="{{ route('export.user') }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export Users
    </a>

@endsection

@push('scripts')
<script>
    let table;

    function initDataTable(lang) {
        // kalau table sudah ada, destroy dulu
        if ($.fn.DataTable.isDataTable('#userTable')) {
            $('#userTable').DataTable().destroy();
        }

        let langUrl = (lang === 'id') 
            ? "/assets/indonesia.json" 
            : "/assets/english.json";

        table = $('#userTable').DataTable({
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
