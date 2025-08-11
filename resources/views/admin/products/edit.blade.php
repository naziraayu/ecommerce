@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('product.edit product') }}</h3>
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

    {{-- Preview gambar lama --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        @foreach ($product->images as $image)
            <div style="position: relative;">
                <img src="{{ asset('storage/' . $image->image) }}" width="150" class="img-thumbnail">
                <form action="{{ route('product-images.destroy', $image->id) }}" method="POST" style="position: absolute; top: 0; right: 0;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus gambar ini?')">×</button>
                </form>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="category_id" class="form-label">{{ __('product.category') }}</label>
                    <select name="category_id" class="form-select select2" required>
                        <option value="">{{ __('product.select') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('product.product name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('product.description') }}</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('product.price') }}</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">{{ __('product.stock') }}</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">{{ __('product.new image') }}</label>
                    <input type="file" name="images[]" class="form-control" multiple>
                    <small class="text-muted">{{ __('product.ignore') }}</small>
                </div>

                <button type="submit" class="btn btn-success">{{ __('product.update product') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Pilih kategori",
            width: '100%'
        });
    });
</script>
@endpush
