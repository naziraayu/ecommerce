<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Failed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-red-600 mb-4">Payment Failed!</h3>
                    <p class="text-gray-600 mb-6">
                        Unfortunately, your payment for Order #{{ $order->id }} could not be processed.
                    </p>
                    
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <p><strong>Order ID:</strong> {{ $order->id }}</p>
                        <p><strong>Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                    </div>
                    
                    <div class="space-x-4">
                        <a href="{{ route('payment.show', $order) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            Try Again
                        </a>
                        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>