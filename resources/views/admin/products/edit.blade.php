@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('product.edit product')}}</h2>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // Data dummy untuk kategori
        $dummyCategories = [
            (object)[
                'id' => 1,
                'name' => 'Kategori A'
            ],
            (object)[
                'id' => 2,
                'name' => 'Kategori B'
            ],
            (object)[
                'id' => 3,
                'name' => 'Kategori C'
            ],
        ];
    @endphp
    <div class="mt-2 d-flex flex-wrap">
        
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="category_id">{{__('product.category')}}</label>
            <select name="category_id" class="form-control select2" required>
                <option value="">{{ __('product.select a category') }}</option>
                @foreach ($dummyCategories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">{{__('product.product name')}}</label>
            <input type="text" name="name" class="form-control" value="" required>
        </div>
        
        <div class="form-group">
            <label for="description">{{__('product.description')}}</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        
        <div class="form-group">
            <label for="price">{{__('product.price')}}</label>
            <input type="number" step="0.01" name="price" class="form-control" value="" required>
        </div>
        
        <div class="form-group">
            <label for="stock">{{__('product.stock')}}</label>
            <input type="number" name="stock" class="form-control" value="" required>
        </div>
        
        <div class="form-group">
            <label for="images">{{__('product.product images')}}</label>
            <input type="file" name="images[]" class="form-control" multiple>
            <small class="form-text text-muted">{{__('product.nb2')}}</small>
        </div>
        
        <button type="submit" class="btn btn-primary">{{__('product.update product')}}</button>
    </form>
@endsection
