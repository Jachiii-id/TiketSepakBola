@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Checkout</h1>

        @if($ticket && $snapToken)
            <p>Detail Tiket Anda:</p>
            <ul>
                <li>ID Pengguna: {{ $ticket->user_id }}</li>
                <li>Jumlah Tiket: {{ $ticket->num_tickets }}</li>
                <li>Total Harga: Rp {{ number_format($ticket->amount, 0, ',', '.') }}</li>
            </ul>

            <div class="row">
                <div class="col-md-6">
                    <h5>Pilih Metode Pembayaran</h5>
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf

                        <div class="custom-card">
                            <div class="custom-card-body" onclick="document.getElementById('bni').checked = true;">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bni" value="BNIVA">
                                    <img src="{{ asset('assets/images/method-logo/logo-bni.png') }}" alt="BNI" class="img-fluid">
                                    <label class="form-check-label" for="bni">BNI</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Bayar Sekarang</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h5>Total Pembayaran</h5>
                    <h2>Rp {{ number_format($ticket->amount, 0, ',', '.') }}</h2>
                </div>
            </div>

            <button id="pay-button" class="btn btn-success mt-3">Bayar dengan Snap</button>
        @else
            <p class="text-danger">Data tiket tidak tersedia. Silakan coba lagi.</p>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
    <script>
        document.getElementById('pay-button')?.addEventListener('click', function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    console.log('Payment Success:', result);
                    alert('Pembayaran berhasil!');
                },
                onPending: function(result) {
                    console.log('Payment Pending:', result);
                    alert('Pembayaran tertunda. Silakan selesaikan pembayaran Anda.');
                },
                onError: function(result) {
                    console.log('Payment Error:', result);
                    alert('Terjadi kesalahan saat memproses pembayaran.');
                },
                onClose: function() {
                    alert('Anda menutup pembayaran tanpa menyelesaikan.');
                }
            });
        });
    </script>
@endsection
