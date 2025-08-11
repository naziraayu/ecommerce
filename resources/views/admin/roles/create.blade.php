@extends('layouts.admin')

@section('content')
<div class="card mt-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('role.create_title') }}</h5>
        <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">{{ __('role.cancel') }}</a>
    </div>
    <div class="card-body">

        {{-- Error alert --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>{{ __('role.error') }}</strong>
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
                <label for="name" class="form-label">{{ __('role.role_name') }}</label>
                <input type="text" name="name" class="form-control" placeholder="{{ __('role.placeholder_role') }}" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="permissions" class="form-label">{{ __('role.permissions') }}</label>
                <select name="permissions[]" id="permissions" class="form-select select2" multiple required>
                    @foreach ($permissions as $permission)
                        <option value="{{ $permission }}" {{ in_array($permission, old('permissions', [])) ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $permission)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">{{ __('role.save') }}</button>
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
