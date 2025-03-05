@extends('auth.layout')

@section('content')
<div class="container">
    <h2>{{__('auth.register')}}</h2>
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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">{{__('auth.name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="password">{{__('auth.password')}}</label>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">{{__('auth.confirm password')}}</label>
            <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}" required>
        </div>
        <div class="form-group">
            <label for="address">{{__('auth.address')}}</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
        </div>
        <div class="form-group">
            <label for="phone_number">{{__('auth.phone number')}}</label>
            <input type="number" name="phone_number" class="form-control" value="{{ old('phone_number') }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">{{__('auth.register')}}</button>
    </form>
</div>
@endsection