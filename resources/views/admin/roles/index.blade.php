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
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm{{ __('role.confirm_delete') }};">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">{{ __('role.delete') }}</button>
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
        $('#roles-table').DataTable({
            language: {
                url: "{{ asset(App::getLocale() === 'id' ? 'assets/indonesia.json' : 'assets/english.json') }}"
            }
        });
    });
</script>
@endpush
