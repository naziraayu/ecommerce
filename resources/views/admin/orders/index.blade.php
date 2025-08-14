@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between mb-4">
        <h2>{{ __('order.list orders') }}</h2>
        <div>
            <a href="{{ route('orders.export') }}" class="btn btn-success" id="exportBtn">
                <i class="fas fa-file-excel"></i> 
                <span class="btn-text">{{ __('order.export') }}</span>
                <span class="spinner-border spinner-border-sm d-none" id="exportSpinner"></span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table id="orderTable" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>{{ __('order.user') }}</th>
                        <th>{{ __('order.total price') }}</th>
                        <th>{{ __('order.status') }}</th>
                        <th>{{ __('order.order date') }}</th>
                        <th>{{ __('order.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'Guest' }}</td>
                            <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                           <td>
                                <select class="form-select form-select-sm order-status" data-id="{{ $order->id }}">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> {{ __('View') }}
                                </a>
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
    // DataTable init (biar gak double init, tambahkan destroy:true kalau reload)
    let table;

    function initDataTable(lang) {
        // kalau table sudah ada, destroy dulu
        if ($.fn.DataTable.isDataTable('#orderTable')) {
            $('#orderTable').DataTable().destroy();
        }

        let langUrl = (lang === 'id') 
            ? "{{ secure_asset('assets/indonesia.json') }}" 
            : "{{ secure_asset('assets/english.json') }}";

        table = $('#orderTable').DataTable({
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


    // Export button
    $('#exportBtn').click(function() {
        $(this).prop('disabled', true);
        $('#exportSpinner').removeClass('d-none');
        $('.btn-text').text("{{ __('Exporting...') }}");
    });

    // Ganti status order via AJAX dengan konfirmasi SweetAlert
    $('.order-status').each(function(){
        // simpan status lama biar bisa dibalikin kalau batal
        $(this).data('old-status', $(this).val());
    });

    $('.order-status').change(function() {
        let dropdown = $(this);
        let orderId = dropdown.data('id');
        let newStatus = dropdown.val();

        Swal.fire({
            title: 'Ubah Status Pesanan?',
            text: "Apakah Anda yakin ingin mengubah status menjadi '" + newStatus + "'?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/orders/${orderId}/status`,
                    method: 'PATCH',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire(
                                'Berhasil!',
                                'Status berhasil diubah ke: ' + res.new_status,
                                'success'
                            );
                            dropdown.data('old-status', newStatus); // update status lama
                        } else {
                            Swal.fire('Gagal!', 'Gagal mengubah status.', 'error');
                            dropdown.val(dropdown.data('old-status')); // balikin
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan, coba lagi.', 'error');
                        dropdown.val(dropdown.data('old-status')); // balikin
                    }
                });
            } else {
                // batal → kembalikan ke status lama
                dropdown.val(dropdown.data('old-status'));
            }
        });
    });
});
</script>



@endpush