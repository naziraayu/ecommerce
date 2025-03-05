@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{__('order.order detail')}} : #</h2>
    <p><strong>{{__('order.user')}} :</strong> </p>
    <p><strong>{{__('order.total price')}} :</strong> Rp </p>
    <p><strong>Status :</strong></p>
    <p><strong>{{__('order.created at')}} :</strong></p>

    <h4>{{__('order.Order Items')}}</h4>
    
    @php
        // Data dummy untuk order dengan items
        $order = (object)[
            'id' => 101,
            'items' => [
                (object)[
                    'product' => (object)['name' => 'Produk A'],
                    'quantity' => 2,
                    'price' => 50000
                ],
                (object)[
                    'product' => (object)['name' => 'Produk B'],
                    'quantity' => 1,
                    'price' => 75000
                ],
                (object)[
                    'product' => (object)['name' => 'Produk C'],
                    'quantity' => 3,
                    'price' => 30000
                ],
            ]
        ];
    @endphp

    <table id="table" class="table table-striped" style="width:100%">
        <thead>
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

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
