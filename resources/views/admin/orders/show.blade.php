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
    <div class="card">
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

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">
        {{ __('order.back to list') }}
    </a>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#itemsTable').DataTable({
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            language: {
                url: "{{ asset(App::getLocale() === 'id' ? 'assets/indonesia.json' : 'assets/english.json') }}"
            }
        });
    });
</script>
@endpush
