<div class="mb-4">
    <h4>{{ __('profil.profile_information') }}</h4>
    <p class="text-muted">
        {{ __('profil.ket') }} 
    </p>
</div>

<form id="send-verification" method="POST" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="name" class="form-label">{{ __('profil.name') }}</label>
        <input type="text" class="form-control" id="name" name="name"
            value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>
        @error('name')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">{{ __('profil.email') }}</label>
        <input type="email" class="form-control" id="email" name="email"
            value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-muted small">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="btn btn-link p-0 align-baseline">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success py-1 px-2 mt-2 small mb-0">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </div>
                @endif
            </div>
        @endif
    </div>
    {{-- Address --}}
    <div class="mb-3">
        <label for="address" class="form-label">{{ __('profil.address') }}</label>
        <input type="text" class="form-control" id="address" name="address"
            value="{{ old('address', $user->address) }}" required autocomplete="address">
        @error('address')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Phone Number --}}
    <div class="mb-3">
        <label for="phone_number" class="form-label">{{ __('profil.phone_number') }}</label>
        <input type="text" class="form-control" id="phone_number" name="phone_number"
            value="{{ old('phone_number', $user->phone_number) }}" required autocomplete="tel">
        @error('phone_number')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary">{{ __('profil.save') }}</button>

        @if (session('status') === 'profile-updated')
            <span class="text-success small">{{ __('Saved.') }}</span>
        @endif
    </div>
</form>
