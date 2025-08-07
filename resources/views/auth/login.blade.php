@extends('auth.layout')

@section('content')
<div class="container">
    <h2>{{ __('auth.login') }}</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ is_array(old('email')) ? '' : old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="password">{{ __('auth.password') }}</label>
            <input type="password" name="password" class="form-control" required>
            <a href="{{ route('password.request') }}" class="text-small">
                {{ __('auth.forgot password') }}?
            </a>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('auth.login') }}</button>
    </form>
</div>
@endsection
