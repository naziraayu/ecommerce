@extends('layouts.admin')

@section('content')
<h2 class="mt-3">{{ __('dashboard.admin_dashboard') }}</h2>

<div class="row">
    <!-- Total Users -->
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">{{ __('dashboard.total_users') }}</div>
            <div class="card-body">
                <h5 class="card-title">
                    {{ \App\Models\User::where('role_id', 2)->count() }}
                </h5>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">{{ __('dashboard.total_products') }}</div>
            <div class="card-body">
                <h5 class="card-title">{{ \App\Models\Product::count() }}</h5>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-md-4">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">{{ __('dashboard.total_orders') }}</div>
            <div class="card-body">
                <h5 class="card-title">{{ \App\Models\Order::count() }}</h5>
            </div>
        </div>
    </div>
</div>

<h4>{{ __('dashboard.recent_orders') }}</h4>
<table id="table" class="table table-striped" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>{{ __('dashboard.user') }}</th>
            <th>{{ __('dashboard.total_price') }}</th>
            <th>Status</th>
            <th>{{ __('dashboard.created_at') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $recentOrders = \App\Models\Order::with('user')
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();
        @endphp

        @foreach($recentOrders as $order)
        <tr>
            <td>{{ $order->id }}</td> 
            <td>{{ $order->user->name ?? '-' }}</td>
            <td>
                {{ $order->total_price !== null 
                    ? 'Rp ' . number_format((float)$order->total_price, 0, ',', '.') 
                    : '-' }}
            </td>
            <td>{{ ucfirst($order->status) }}</td>
            <td>{{ $order->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('scripts')
<script>
    let table;

    function initDataTable(lang) {
        // kalau table sudah ada, destroy dulu
        if ($.fn.DataTable.isDataTable('#table')) {
            $('#table').DataTable().destroy();
        }

        let langUrl = (lang === 'id') 
            ? "/assets/indonesia.json" 
            : "/assets/english.json";

        table = $('#table').DataTable({
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
