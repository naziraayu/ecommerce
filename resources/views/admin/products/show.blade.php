
@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mt-3">{{ __('product.product details') }} : #{{ $product->id }}</h2>

    <div class="mb-3">
        <p><strong>{{ __('product.code') }} :</strong> {{ $product->code }}</p>
        <p><strong>{{ __('product.category') }} :</strong> {{ $product->category->name ?? '-' }}</p>
        <p><strong>{{ __('product.name') }} :</strong> {{ $product->name }}</p>
        <p><strong>{{ __('product.description') }} :</strong> {{ $product->description }}</p>
        <p><strong>{{ __('product.price') }} :</strong> Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        <p><strong>{{ __('product.stock') }} :</strong> {{ $product->stock }}</p>
        <p><strong>{{ __('product.created') }} :</strong> {{ $product->created_at->format('d M Y H:i') }}</p>
        <p><strong>{{ __('product.updated') }} :</strong> {{ $product->updated_at->format('d M Y H:i') }}</p>
    </div>

    <h4>{{ __('product.images') }}</h4>
    <div class="row">
        @forelse($product->images as $image)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <img src="{{ asset('storage/' . $image->image) }}" class="card-img-top" alt="Product Image">
                    <div class="card-body p-2">
                        <small class="text-muted">{{ $image->created_at->format('d M Y H:i') }}</small>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">{{ __('product.no images available') }}</p>
        @endforelse
    </div>

    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">
        {{ __('product.back to list') }}
    </a>
</div>
@endsection
