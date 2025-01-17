<?php

namespace App\Http\Controllers;

use App\Models\Banners;
use App\Models\BlockedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Services\TokopayService;
use App\Models\Matches;
use App\Models\Seats;
use App\Models\User;
use App\Models\Tickets;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
// use Spatie\Ip\IpFactory as Factory;
use IPLib\Factory;



class TicketController extends Controller
{
    protected $tokopayService;

    public function __construct(TokopayService $tokopayService)
    {
        $this->tokopayService = $tokopayService;
    }

    private function handlePurchase(array $validated)
    {
        $response = $this->tokopayService->createOrder(
            $validated['reff_id'],
            $validated['channel'],
            $validated['amount'],
            $validated['customer_name'],
            $validated['customer_email'],
            $validated['customer_phone'],
            $validated['redirect_url'],
            $validated['items']
        );

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Log the decoded response
        // Log::info('CreateOrder response:', ['response' => $decodedResponse]);

        // Check the status and log any errors
        if ($decodedResponse['status'] === 0) {
            Log::error('CreateOrder error:', ['error_msg' => $decodedResponse['error_msg']]);
            // throw new \Exception($decodedResponse['error_msg']);
        }

        try {
            // dd($validated);
            $payment = Payment::create([
                'reference' => $decodedResponse['data']['trx_id'],
                'reff_id' => $validated['reff_id'],
                'status' => 'unpaid',
                'customer_email' => $validated['customer_email'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'payment_channel' => $validated['channel'],
                'ip_address' => $validated['ip_address'],
                'device_id' => $validated['device_id'],
                'platform' => $validated['platform'],
                'browser' => $validated['browser'],
                'total_harga' => $validated['amount'],
                'total_dibayar' => $decodedResponse['data']['total_bayar'],
                'total_diterima' => $decodedResponse['data']['total_diterima'],
            ]);

            // dd($payment);
            // Find the user_id from the authenticated user
            $user_id = Auth::id();

            // Find the ticket associated with the user_id
            $ticket = Tickets::where('user_id', $user_id)->first();

            if ($ticket) {
                // Update the ticket's payment_id with the payment's id
                $ticket->payment_id = $payment->id;
                $ticket->save();

                Log::debug('Updated payment_id on ticket:', ['ticket' => $ticket]);
            } else {
                Log::error('Ticket record not found for user_id:', ['user_id' => $user_id]);
            }
            Log::info('Payment created successfully', ['payment' => $payment]);
        } catch (\Exception $e) {
            Log::error('Failed to create payment', ['error' => $e->getMessage()]);
        }


        // Store the reff_id and response in the session
        // session(['reff_id' => $validated['reff_id']]);
        // session(['trx' => $response]);

        return $response;
    }

    public function storeTicket(Request $request)
    {
        // Retrieve APP_URL from the environment configuration
        $appUrl = env('APP_URL');

        // Validate the hCaptcha response
        // $hcaptchaResponse = $request->input('h-captcha-response');
        // $secretKey = config('services.hcaptcha.secret_key');
        // dd($hcaptchaResponse);

        // $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
        //     'secret' => $secretKey,
        //     'response' => $hcaptchaResponse,
        // ]);

        // $responseData = $response->json();
        // dd($responseData);
        // if (!$responseData['success']) {
        //     // hCaptcha verification failed
        //     return redirect()->back()->withErrors(['hcaptcha' => 'hCaptcha verification failed. Please try again.']);
        // }

        // Get the client's IP address and User-Agent string
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Parse the User-Agent string
        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        // Get additional client information
        $request['ip_address'] = $ipAddress;
        $request['device_id'] = $agent->device();
        $request['platform'] = $agent->platform();
        $request['browser'] = $agent->browser();

        // $platform = $agent->platform();
        // $browser = $agent->browser();
        // $isMobile = $agent->isMobile();
        // $isTablet = $agent->isTablet();
        // $isDesktop = $agent->isDesktop();
        // $languages = $request->getLanguages();
        // $referrer = $request->headers->get('referer');

        // Log the IP address and client information for debugging
        // Log::info('-- New Ticket Purchase --');
        // Log::info('Client IP Address:', ['ip' => $ipAddress]);
        // Log::info('Client Device:', ['device' => $device]);
        // Log::info('Client Platform:', ['platform' => $platform]);
        // Log::info('Client Browser:', ['browser' => $browser]);
        // Log::info('Is Mobile:', ['isMobile' => $isMobile]);
        // Log::info('Is Tablet:', ['isTablet' => $isTablet]);
        // Log::info('Is Desktop:', ['isDesktop' => $isDesktop]);
        // Log::info('Client Languages:', ['languages' => $languages]);
        // Log::info('Referrer URL:', ['referrer' => $referrer]);
        // Log::info('-- End Ticket Purchase --');

        // dd($request->all());

        // Check for duplicate orders
        $existingPayment = Payment::where('ip_address', $ipAddress)
            ->first();

        if ($existingPayment) {
            return redirect()->back()->withErrors(['duplicate' => 'Multiple orders from the same device or IP address are not allowed.']);
        }

        // Check honeypot field
        if ($request->filled('total')) {
            return redirect()->back()->withErrors(['spam' => 'Spam detected.']);
        }

        // Handle ticket info form submission
        $validated = $request->validate([
            'nik' => 'required|array',
            'nik.0' => 'required|numeric|digits:16',
            'nik.1' => 'nullable|numeric|digits:16',

            'nama' => 'required|array',
            'nama.0' => 'required|string|max:255',
            'nama.1' => 'nullable|string|max:255',

            'nomor_hp' => 'required|array',
            'nomor_hp.0' => 'required|numeric|digits_between:9,16',
            'nomor_hp.1' => 'nullable|numeric|digits_between:9,16',

            'email' => 'required|array',
            'email.0' => 'required|email|max:255|unique:users,email',
            'email.1' => 'nullable|email|max:255|unique:users,email',

            'tanggal_lahir' => 'required|array',
            'tanggal_lahir.0' => 'required|date',
            'tanggal_lahir.1' => 'nullable|date',

            'gender' => 'required|array',
            'gender.0' => 'required|in:male,female',
            'gender.1' => 'nullable|in:male,female',

            'amount' => 'required|numeric',
            'id_match' => 'required|numeric',

            'ticket_type' => 'required|string',
            'ticket_quantity' => 'required|integer|min:1|max:2',

            'payment_method' => 'required|string'
        ], [
            'nik.0.required' => 'The NIK for the first ticket buyer is required.',
            'nik.0.numeric' => 'The NIK for the first ticket buyer must be a number.',
            'nik.0.digits' => 'The NIK for the first ticket buyer must be 16 digits.',
            'nik.1.numeric' => 'The NIK for the second ticket buyer must be a number.',
            'nik.1.digits' => 'The NIK for the second ticket buyer must be 16 digits.',
            'nama.0.required' => 'The name for the first ticket buyer is required.',
            'nama.1.required' => 'The name for the second ticket buyer is required.',
            'nomor_hp.0.required' => 'The phone number for the first ticket buyer is required.',
            'nomor_hp.1.required' => 'The phone number for the second ticket buyer is required.',
            'email.0.required' => 'The email for the first ticket buyer is required.',
            'email.0.unique' => 'The email for the first ticket buyer is already in use.',
            'email.1.unique' => 'The email for the second ticket buyer is already in use.',
            'tanggal_lahir.0.required' => 'The birth date for the first ticket buyer is required.',
            'tanggal_lahir.1.required' => 'The birth date for the second ticket buyer is required.',
            'gender.0.required' => 'The gender for the first ticket buyer is required.',
            'gender.1.required' => 'The gender for the second ticket buyer is required.',
            'amount.required' => 'The amount is required.',
            'id_match.required' => 'The match ID is required.',
            'ticket_type.required' => 'The ticket type is required.',
            'ticket_quantity.required' => 'The ticket quantity is required.',
            'payment_method' => 'The payment method is required.',
        ]);

        // Validate the second ticket buyer if the quantity is 2
        if ($request->input('nik.1')) {
            $validated = $request->validate([
                'nama.1' => 'required|string|max:255',
                'nomor_hp.1' => 'required|numeric|digits_between:9,16',
                'email.1' => 'required|email|max:255',
                'tanggal_lahir.1' => 'required|date',
                'gender.1' => 'required|in:male,female',
            ], [
                'nama.1.required' => 'The name for the second ticket buyer is required.',
                'nomor_hp.1.required' => 'The phone number for the second ticket buyer is required.',
                'email.1.required' => 'The email for the second ticket buyer is required.',
                'tanggal_lahir.1.required' => 'The birth date for the second ticket buyer is required.',
                'gender.1.required' => 'The gender for the second ticket buyer is required.',
            ]);
        }

        // Generate a unique reff_id with the prefix 'TTCK_' and uuid7
        $reff_id = 'TTCK_' . Uuid::uuid7()->toString();

        // Construct the redirect URL using APP_URL
        $redirect_url = $appUrl . '/transaksi/' . $reff_id;

        // get data from model Seat where id = ticket_type
        $seat = Seats::find($request['ticket_type']);

        // Check if the Seat record was found
        if (!$seat) {
            return redirect()->back()->with('error', 'Pilihan tempat duduk tidak ditemukan!');
        }

        // Protect from gabut people change price manually on request
        if ($request['amount'] != $seat->price) {
            return redirect()->back()->with('error', 'Jangan nackal ya! Seenaknya ngubah harga tiket!');
        }

        // Add needed data to the request
        $request['reff_id'] = $reff_id;
        $request['channel'] = $request['payment_method'];
        // $request['amount'] = $request['amount'];
        $request['customer_name'] = $request['nama'][0];
        $request['customer_email'] = $request['email'][0];
        $request['customer_phone'] = $request['nomor_hp'][0];
        $request['redirect_url'] = $redirect_url;
        $request['items'] = [
            [
                'ticket_id' => $request['ticket_type'],
                'name' => $request['nama'][0],
                'email' => $request['email'][0],
                'nomor_hp' => $request['nomor_hp'][0],
                'price' => $request['amount'],
                'product_url' => $appUrl . '/tiket/detail-pertandingan/' . $request['id_match'],
            ],
            [
                'ticket_id' => $request['ticket_type'],
                'name' => $request['nama'][1] ?? '',
                'email' => $request['email'][1] ?? '',
                'nomor_hp' => $request['nomor_hp'][1] ?? '',
                'price' => $request['amount'],
                'product_url' => $appUrl . '/tiket/detail-pertandingan/' . $request['id_match'],
            ]
        ];

        if ($validated['ticket_quantity'] > 1) {
            $items[] = [
                'ticket_id' => $validated['ticket_type'] ?? '',
                'name' => $validated['nama'][1] ?? '',
                'email' => $validated['email'][1] ?? '',
                'nomor_hp' => $validated['nomor_hp'][1] ?? '',
                'tanggal_lahir' => $validated['tanggal_lahir'][1] ?? '',
                'gender' => $validated['gender'][1] ?? ''
            ];
        }

        try {
            DB::beginTransaction();

            // Insert data into the user table
            $user = User::create([
                'role_id' => 2, // Default role_id
                'registration_date' => now(),
                'nik' => $validated['nik'][0],
                'name' => $validated['nama'][0],
                'email' => $validated['email'][0],
                'number' => $validated['nomor_hp'][0],
                'birth_date' => $validated['tanggal_lahir'][0],
                'gender' => $validated['gender'][0]
            ]);
            Log::info('User created successfully', ['user' => $user]);

            Auth::login($user);

            $ticketData = [
                [
                    'ticket_type' => $validated['ticket_type'] ?? '',
                    'nik' => $validated['nik'][0] ?? '',
                    'name' => $validated['nama'][0] ?? '',
                    'email' => $validated['email'][0] ?? '',
                    'number' => $validated['nomor_hp'][0] ?? '',
                    'birth_date' => $validated['tanggal_lahir'][0] ?? '',
                    'gender' => $validated['gender'][0] ?? '',
                ]
            ];

            if ($validated['ticket_quantity'] > 1) {
                $ticketData[] = [
                    'ticket_type' => $validated['ticket_type'] ?? '',
                    'nik' => $validated['nik'][1] ?? '',
                    'name' => $validated['nama'][1] ?? '',
                    'email' => $validated['email'][1] ?? '',
                    'number' => $validated['nomor_hp'][1] ?? '',
                    'birth_date' => $validated['tanggal_lahir'][1] ?? '',
                    'gender' => $validated['gender'][1] ?? ''
                ];
            }

            // Insert data into the ticket table
            $ticket = Tickets::create([
                'user_id' => $user->id,
                'match_id' => $validated['id_match'],
                'seat_id' => $validated['ticket_type'],
                'num_tickets' => $validated['ticket_quantity'],
                'amount' => $validated['amount'],
                'ticket_data' => json_encode($ticketData),
            ]);
            Log::info('Ticket created successfully', ['ticket' => $ticket]);

            $response = $this->handlePurchase($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating ticket and payment: ' . $e->getMessage(), ['exception' => $e]);
            // Handle the error, log it, and return an appropriate response
            return redirect()->back()->with('error', 'An error occurred while creating the ticket and payment.');
        }

        $response = json_decode($response, true);

    }

    public function showTickets()
    {
        // Fetch data from the matches table
        $matches = Matches::with('getClub1')->get();

        // Add the breadcrumbs
        $breadcrumbs = [
            ['name' => 'Beranda', 'url' => route('index')],
        ];

        $images = Banners::all();

        // Pass the data to the view
        return view('pages.tickets', compact('matches', 'images', 'breadcrumbs'));
    }

    public function showDetail($id)
    {
        // Find the match based on the ID
        $matches = Matches::find($id);
        if (!$matches) {
            abort(404, 'Pertandingan tidak ditemukan');
        }

        // Ambil detail pertandingan dengan relasi
        $match_detail = Matches::with('getClub1')->find($id);
        if (!$match_detail) {
            abort(404, 'Detail pertandingan tidak ditemukan');
        }

        // Ambil tipe tiket
        $getTicketTypes = Seats::all();

        // Mengatur breadcrumbs jika diperlukan
        $breadcrumbs = [
            ['name' => 'Beranda', 'url' => route('index')],
            ['name' => $matches->name, 'url' => route('match.detail', ['id' => $matches->id])],
            ['name' => 'Pembayaran', 'url' => url()->current()],
            // ['url' => route('tickets.index'), 'label' => 'Tiket'],
            // ['url' => route('ticket.detail', ['id' => $id]), 'label' => 'Detail Tiket'],
        ];

        $ticket = Tickets::findOrFail($id);
        return view('pages.ticket-detail', compact('matches', 'match_detail', 'getTicketTypes', 'breadcrumbs'));
    }

}
