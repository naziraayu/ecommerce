@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('admin.edit admin') }}</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
            <form action="{{ route('admins.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('auth.name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('auth.password') }} ({{ __('Optional') }})</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank if not changing">
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('auth.confirm password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">{{ __('auth.address') }}</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $admin->address) }}">
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">{{ __('auth.phone number') }}</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $admin->phone_number) }}">
                </div>

                <button type="submit" class="btn btn-primary">{{ __('admin.update role') }}</button>
            </form>
        </div>
    </div>
@endsection
