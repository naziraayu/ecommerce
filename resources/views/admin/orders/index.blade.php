@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('order.list orders')}}</h2>

    @php
        // Data dummy untuk orders
        $dummyOrders = [
            (object)[
                'id' => 101,
                'user' => (object)['name' => 'John Doe'],
                'total_price' => 250000,
                'status' => 'completed'
            ],
            (object)[
                'id' => 102,
                'user' => (object)['name' => 'Jane Smith'],
                'total_price' => 150000,
                'status' => 'pending'
            ],
            (object)[
                'id' => 103,
                'user' => (object)['name' => 'Alice Johnson'],
                'total_price' => 300000,
                'status' => 'cancelled'
            ],
        ];
    @endphp

    <table id="table" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>{{ __('order.user') }}</th>
                <th>{{ __('order.total price') }}</th>
                <th>Status</th>
                <th>{{ __('order.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dummyOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection