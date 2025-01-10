<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Seats;
use App\Models\Tickets;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    public function process(Request $request)
{
    Log::info('Checkout Process Request:', $request->all());

    try {
        // Validasi data
        $validated = $request->validate([
            'id_match' => 'required|exists:matches,id',
            'ticket_type' => 'required|exists:seats,id',
            'ticket_quantity' => 'required|integer|min:1|max:2',
            'amount' => 'required|numeric',
            'nama' => 'required|array|min:1',
            'nama.0' => 'required|string|max:255',
            'email' => 'required|array|min:1',
            'email.0' => 'required|email|max:255',
            'nomor_hp' => 'required|array|min:1',
            'nomor_hp.0' => 'required|numeric|digits_between:9,16',
            'payment_method' => 'required|string',
        ]);

        // Hitung total
        $total = $validated['amount'] * $validated['ticket_quantity'];

        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Buat parameter transaksi
        $params = [
            'transaction_details' => [
                'order_id' => 'TICKET-' . time(),
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $validated['nama'][0],
                'email' => $validated['email'][0],
                'phone' => $validated['nomor_hp'][0],
            ],
        ];

        // Dapatkan URL Snap Redirect
        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        // Redirect pengguna ke halaman pembayaran Midtrans
        return redirect($paymentUrl);

    } catch (\Exception $e) {
        // Log error
        Log::error('Error in process:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memproses pembayaran.']);
    }
}


    
    public function show($ticket, $snapToken)
    {
        // Find the ticket details
        $ticket = Tickets::findOrFail($ticket);
        
        // Retrieve the product reference or relevant data for the ticket
        // Assuming you need to access match details or other related information
        $reference = [
            'name' => $ticket->match->name, // Assuming you want the match name
            // Add other necessary fields here
        ];
    
        // You may also want to pass payment details or other related info
        $payment_channel = 'BRIVA'; // Example payment method, replace as needed
        $total_harga = $ticket->amount; // Total amount for the ticket
        
        return view('pages.checkout', compact('ticket', 'snapToken', 'reference', 'payment_channel', 'total_harga'));
    }

    public function notificationHandler(Request $request)
{
    Log::info('Midtrans Notification:', $request->all());

    $notification = $request->all();
    $transactionStatus = $notification['transaction_status'];
    $orderId = $notification['order_id'];

    // Cari pembayaran berdasarkan order_id
    $payment = Payment::where('reference', $orderId)->first();

    if (!$payment) {
        return response()->json(['message' => 'Payment not found'], 404);
    }

    // Update status pembayaran
    if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
        $payment->status = 'success';
    } elseif ($transactionStatus == 'pending') {
        $payment->status = 'pending';
    } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
        $payment->status = 'failed';
    }

    $payment->save();

    return response()->json(['message' => 'Notification processed successfully']);
}

    

    // public function checkout(Payment $payment){
    //     $reference = config('reference');
    //     $product = collect($products)->firstWhere('id', $transaction->product_id);

    //     return view('checkout',  compact('transaction', 'product'));
    // }
}
