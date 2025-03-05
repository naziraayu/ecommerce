@extends('layouts.admin')

@section('content')
<h2 class="mt-3">{{__('product.products')}}</h2>

<a href="{{ route('products.create') }}" class="btn btn-primary mb-3">{{__('product.add')}}</a>

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@php
    // Data dummy untuk produk
    $dummyProducts = [
        (object)[
            'id' => 1,
            'name' => 'Produk A',
            'description' => 'Deskripsi untuk Produk A',
            'price' => 100000,
            'stock' => 10,
            'images' => 'produk-a.jpg'
        ],
        (object)[
            'id' => 2,
            'name' => 'Produk B',
            'description' => 'Deskripsi untuk Produk B',
            'price' => 150000,
            'stock' => 5,
            'images' => 'produk-b.jpg'
        ],
        (object)[
            'id' => 3,
            'name' => 'Produk C',
            'description' => 'Deskripsi untuk Produk C',
            'price' => 200000,
            'stock' => 8,
            'images' => 'produk-c.jpg'
        ],
    ];
@endphp

<table id="productTable" class="table table-striped" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>{{ __('product.name') }}</th>
            <th>{{ __('product.description') }}</th>
            <th>{{ __('product.price') }}</th>
            <th>{{ __('product.stock') }}</th>
            <th>{{ __('product.images') }}</th>
            <th>{{ __('product.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dummyProducts as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->description }}</td>
                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <img src="{{ asset('images/' . $product->images) }}" alt="{{ $product->name }}" width="50">
                </td>
                <td>
                    <a href="{{ route('products.edit', 1) }}" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection