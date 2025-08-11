@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('product.add') }}</h3>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('product.cancel') }}</a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ __('product.error occurred') }}</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="category_id" class="form-label">{{ __('product.category') }}</label>
                    <select name="category_id" class="form-select select2" required>
                        <option value="">{{ __('product.select') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('product.product name') }}</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('product.product name') }}</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('product.price') }}</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">{{ __('product.stock') }}</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">{{ __('product.images') }}</label>
                    <input type="file" name="images[]" class="form-control" multiple required>
                    <small class="text-muted">{{ __('product.nb') }}</small>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('categories.save') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endpush
