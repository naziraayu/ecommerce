<div class="mb-4">
    <h4 class="text-danger">{{ __('profil.delete_account') }}</h4>
    <p class="text-muted">
        {{ __('profil.once_deleted_warning') }}
    </p>

    <!-- Tombol trigger modal -->
    <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        {{ __('profil.delete_account') }}
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('profile.destroy') }}" class="modal-content">
            @csrf
            @method('DELETE')

            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">{{ __('profil.are_you_sure_delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('profil.cancel') }}"></button>
            </div>

            <div class="modal-body">
                <p>
                    {{ __('profil.delete_confirm_message') }}
                </p>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('profil.password') }}</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="{{ __('profil.password') }}" required>
                    @error('password', 'userDeletion')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('profil.cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('profil.delete_account') }}</button>
            </div>
        </form>
    </div>
</div>
