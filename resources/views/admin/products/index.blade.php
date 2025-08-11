@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('product.management') }}</h3>
        <a href="{{ route('products.create') }}" class="btn btn-primary">{{ __('product.add') }}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table id="products-table" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('product.code') }}</th>
                        <th>{{ __('product.category') }}</th>
                        <th>{{ __('product.product name') }}</th>
                        <th>{{ __('product.description') }}</th>
                        <th>{{ __('product.price') }}</th>
                        <th>{{ __('product.stock') }}</th>
                        <th>{{ __('product.images') }}</th>
                        <th>{{ __('product.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->description }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                @if ($product->images->count())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($product->images as $image)
                                            <img src="{{ asset('storage/' . $image->image) }}" width="60" class="img-thumbnail">
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#products-table').DataTable({
            language: {
                url: "{{ asset(App::getLocale() === 'id' ? 'assets/indonesia.json' : 'assets/english.json') }}"
            },
            scrollX: true
        });
    });
</script>
@endpush
