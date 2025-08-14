@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mt-3">{{ __('order.order detail') }} : #{{ $order->id }}</h2>

    <div class="mb-3">
        <p><strong>{{ __('order.user') }} :</strong> {{ $order->user->name }}</p>
        <p><strong>{{ __('order.total price') }} :</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        <p><strong>Status :</strong> {{ ucfirst($order->status) }}</p>
        <p><strong>{{ __('order.created at') }} :</strong> {{ $order->created_at->format('d M Y') }}</p>
    </div>

    <h4>{{ __('order.Order Items') }}</h4>
    <div class="card mb-4">
        <div class="card-body">
            <table id="itemsTable" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('order.Product') }}</th>
                        <th>{{ __('order.Quantity') }}</th>
                        <th>{{ __('order.Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail Pembayaran --}}
    <h4>{{ __('order.payment details') }}</h4>
    <div class="card">
        <div class="card-body">
            <p><strong>{{ __('order.payment status') }} :</strong> {{ ucfirst($order->payment_status) }}</p>
            <p><strong>{{ __('order.payment type') }} :</strong> {{ ucfirst($order->payment_type ?? '-') }}</p>
            <p><strong>{{ __('order.Transaction ID') }} :</strong> {{ $order->midtrans_transaction_id ?? '-' }}</p>
            <p><strong>{{ __('order.paid at') }} :</strong> 
                {{ $order->paid_at ? $order->paid_at->format('d M Y H:i') : '-' }}
            </p>
        </div>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">
        {{ __('order.back to list') }}
    </a>
</div>
@endsection

@push('scripts')
<script>
    let table;

    function initDataTable(lang) {
        // kalau table sudah ada, destroy dulu
        if ($.fn.DataTable.isDataTable('#itemsTable')) {
            $('#itemsTable').DataTable().destroy();
        }

        let langUrl = (lang === 'id') 
            ? "{{ secure_asset('assets/indonesia.json') }}" 
            : "{{ secure_asset('assets/english.json') }}";

        table = $('#itemsTable').DataTable({
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

@endpush
