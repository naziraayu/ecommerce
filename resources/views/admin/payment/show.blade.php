@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3>Payment - Order #{{ $order->id }}</h3>
    <p>Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>

    <button id="pay-button" class="btn btn-primary mt-3">Bayar Sekarang</button>
</div>

<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
</script>
<script type="text/javascript">
    document.getElementById('pay-button').addEventListener('click', function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                alert("Pembayaran berhasil!");
                console.log(result);
            },
            onPending: function(result) {
                alert("Menunggu pembayaran!");
                console.log(result);
            },
            onError: function(result) {
                alert("Pembayaran gagal!");
                console.log(result);
            },
            onClose: function() {
                alert("Anda menutup popup tanpa menyelesaikan pembayaran");
            }
        });
    });
</script>
@endsection
