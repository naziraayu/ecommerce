@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('profil.profile') }}</h3>

    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            {{ __('Profil berhasil diperbarui.') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            {{ __('profil.profile_information') }}
        </div>
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            {{ __('profil.update_password') }}
        </div>
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            {{ __('profil.delete_account') }}
        </div>
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#categories-table').DataTable({
            language: {
                url: "{{ asset(App::getLocale() === 'id' ? 'assets/indonesia.json' : 'assets/english.json') }}"
            }
        });
    });
</script>
@endpush