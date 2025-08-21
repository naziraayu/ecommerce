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
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger delete-btn">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tombol Ekspor & Impor --}}
    <a href="{{ route('product.export') }}" class="btn btn-success mt-3">{{ __('product.export') }}</a>
    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#importModal">{{ __('product.import') }}</button>
    <a href="{{ route('product.downloadTemplate') }}" class="btn btn-link mt-3">{{ __('product.download template') }}</a>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('product.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">{{ __('product.import') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('product.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('product.import') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    let table;

    function initDataTable(lang) {
        // kalau table sudah ada, destroy dulu
        if ($.fn.DataTable.isDataTable('#products-table')) {
            $('#products-table').DataTable().destroy();
        }

        let langUrl = (lang === 'id') 
            ? "/assets/indonesia.json" 
            : "/assets/english.json";

        table = $('#products-table').DataTable({
            processing: true,
            serverSide: false,
            language: {
                url: langUrl
            }
        });
    }

    $(document).ready(function () {
        // inisialisasi pertama sesuai locale Laravel
        let lang = "{{ app()->getLocale() }}";
        initDataTable(lang);

        // contoh: kalau user ganti bahasa (misal pakai select)
        $('#languageSelect').change(function() {
            let newLang = $(this).val();
            initDataTable(newLang);
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Hapus produk pakai SweetAlert2
        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Yakin hapus produk?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Notifikasi sukses dari session (opsional)
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush
