@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Checkout</h1>
        <p>Detail Tiket Anda:</p>
        <ul>
            <li>ID Tiket: {{ $ticket->user_id }}</li>
            <li>Jumlah Tiket: {{ $ticket->num_tickets }}</li>
            <li>Total Harga: Rp {{ number_format($ticket->amount, 0, ',', '.') }}</li>
        </ul>

        <!-- Tombol untuk proses pembayaran -->
        <button id="pay-button">Bayar Sekarang</button>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    console.log('success', result);
                },
                onPending: function(result) {
                    console.log('pending', result);
                },
                onError: function(result) {
                    console.log('error', result);
                }
            });
        });
    </script>
@endsection
