@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('setting.Change Email') }}</h2>

    {{-- Alert sukses --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Alert error --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validasi error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- STEP 1: Verifikasi email lama + password --}}
    <form action="{{ route('settings.checkEmail') }}" method="POST" class="mb-4">
        @csrf
        <div class="form-group">
            <label for="old_email">Email Lama</label>
            <input type="email" name="old_email" id="old_email"
                   class="form-control"
                   value="{{ old('old_email', auth()->user()->email) }}"
                   required>
        </div>

        <div class="form-group mt-2">
            <label for="password">Password Saat Ini</label>
            <input type="password" name="password" id="password"
                   class="form-control" required>
        </div>

        <button type="submit" class="btn btn-info mt-3">
            Cek
        </button>
    </form>

    {{-- STEP 2: Jika cek berhasil, tampilkan field email baru --}}
    @if (session('verified_old'))
        <form action="{{ route('settings.sendVerification') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="new_email">Email Baru</label>
                <input type="email" name="new_email" id="new_email"
                       class="form-control"
                       value="{{ old('new_email') }}" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">
                Kirim Verifikasi
            </button>
        </form>
    @endif
@endsection
