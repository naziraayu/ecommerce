@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('setting.Change Email') }}</h2>

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

    <form action="{{ route('settings.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">{{ __('setting.New Email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">{{ __('setting.Create Email') }}</button>
    </form>
@endsection
