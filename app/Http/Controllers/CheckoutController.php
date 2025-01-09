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
    $appUrl = env('APP_URL');

    try {
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

        $reff_id = 'TTCK_' . Uuid::uuid7()->toString();

        $seat = Seats::findOrFail($validated['ticket_type']);
        if ($validated['amount'] != $seat->price) {
            return redirect()->back()->withErrors(['error' => 'Harga tiket tidak valid.']);
        }

        $total = $validated['amount'] * $validated['ticket_quantity'];

        DB::beginTransaction();

        $user = User::firstOrCreate(
            ['email' => $validated['email'][0]],
            ['name' => $validated['nama'][0], 'number' => $validated['nomor_hp'][0]]
        );

        $ticket = Tickets::create([
            'user_id' => $user->id,
            'match_id' => $validated['id_match'],
            'seat_id' => $validated['ticket_type'],
            'num_tickets' => $validated['ticket_quantity'],
            'amount' => $total,
            'ticket_data' => json_encode($validated),
        ]);

        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => 'TICKET-' . $ticket->id,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $validated['nama'][0],
                'email' => $validated['email'][0],
                'phone' => $validated['nomor_hp'][0],
            ],
        ]);

        Payment::create([
            'reference' => 'REF-' . time(),
            'status' => 'pending', // Pastikan nilai ini sesuai dengan skema
            'snap_token' => $snapToken,
            'customer_email' => $validated['email'][0],
            'customer_name' => $validated['nama'][0],
            'customer_phone' => $validated['nomor_hp'][0],
            'payment_channel' => $validated['payment_method'], // Harus valid
            'total_harga' => $total,
            'total_dibayar' => 0,
        ]);
        DB::commit();

        return redirect()->route('checkout', ['ticket' => $ticket->id, 'snapToken' => $snapToken])
            ->with('success', 'Checkout berhasil! Lanjutkan ke pembayaran.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error saat memproses checkout:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memproses permintaan.']);
    }
}

    
    public function show($ticket, $snapToken)
    {
        $ticket = Tickets::findOrFail($ticket);

        return view('pages.checkout', compact('ticket', 'snapToken'));
    }
}
