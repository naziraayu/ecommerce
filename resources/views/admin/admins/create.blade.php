@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('admin.add') }}</h3>
        <a href="{{ route('admins.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admins.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('auth.name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('auth.password') }}</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('auth.confirm password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">{{ __('auth.address') }}</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">{{ __('auth.phone number') }}</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" required>
                </div>

                <div class="mb-3">
                    <label for="role_id" class="form-label">{{ __('admin.role name') }}</label>
                    <select name="role_id" class="form-control" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
               

                <button type="submit" class="btn btn-primary">{{ __('auth.register') }}</button>
            </form>
        </div>
    </div>
@endsection
