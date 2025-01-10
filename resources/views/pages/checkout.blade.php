@extends('layouts.app-plain')

@section('title', 'Checkout - Tactick')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card">
        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
            @csrf
            <p>Anda akan melakukan pembelian produk <strong>{{ $reference['name'] }}</strong> dengan harga
                <strong>Rp{{ number_format($total_harga, 0, ',', '.') }}</strong>
            </p>
            <p>Metode Pembayaran: <strong>{{ $payment_channel }}</strong></p>
            <button type="button" class="btn btn-primary mt-3" id="pay-button">
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
    document.getElementById('pay-button')?.addEventListener('click', function() {
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
