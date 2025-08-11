<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment') }} - Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Order Summary -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                        <div class="border rounded-lg p-4">
                            @foreach($order->orderItems as $item)
                            <div class="flex justify-between items-center mb-2">
                                <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            <hr class="my-4">
                            <div class="flex justify-between items-center font-bold">
                                <span>Total:</span>
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Payment Status</h3>
                        <span class="px-4 py-2 rounded-full text-sm font-medium
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>

                    <!-- Payment Button -->
                    @if($order->payment_status === 'pending' && $snapToken)
                    <div class="text-center">
                        <button id="pay-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg">
                            Pay Now
                        </button>
                    </div>
                    @elseif($order->payment_status === 'paid')
                    <div class="text-center">
                        <p class="text-green-600 font-semibold text-lg">Payment Completed!</p>
                        <a href="{{ route('dashboard') }}" class="mt-4 inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Back to Dashboard
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($snapToken)
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    console.log(result);
                    window.location.href = '{{ route("payment.success", $order) }}';
                },
                onPending: function(result){
                    console.log(result);
                    alert("Waiting for payment confirmation");
                },
                onError: function(result){
                    console.log(result);
                    window.location.href = '{{ route("payment.failed", $order) }}';
                },
                onClose: function(){
                    alert('Payment cancelled');
                }
            });
        };
    </script>
    @endif
</x-app-layout>