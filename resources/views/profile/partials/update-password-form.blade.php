<div class="mb-4">
    <h4>{{ __('Update Password') }}</h4>
    <p class="text-muted">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
        <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">{{ __('New Password') }}</label>
        <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

    @if (session('status') === 'password-updated')
        <div class="alert alert-success mt-3" role="alert">
            {{ __('Saved.') }}
        </div>
    @endif
</form>
