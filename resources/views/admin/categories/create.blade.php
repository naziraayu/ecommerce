@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('categories.add') }}</h3>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">{{ __('categories.cancel') }}</a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ __('categories.error occurred') }}</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('categories.category name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label"> #products-table</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('categories.save') }}</button>
            </form>
        </div>
    </div>
@endsection
