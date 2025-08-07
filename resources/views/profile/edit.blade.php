@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('Profile') }}</h3>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            {{ __('Profil berhasil diperbarui.') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            {{ __('Informasi Profil') }}
        </div>
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            {{ __('Ubah Password') }}
        </div>
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            {{ __('Hapus Akun') }}
        </div>
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
