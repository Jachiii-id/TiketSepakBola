<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetailMatchController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;

// Halaman Utama dan Tiket
Route::get('/', [TicketController::class, 'showTickets'])->name('index');
Route::get('/tickets', [TicketController::class, 'showTickets'])->name('tickets.index');
Route::get('/tiket/detail-pertandingan/{id}', [DetailMatchController::class, 'showDetailMatches'])->name('match.detail');
Route::get('/tiket/detail/{id}', [TicketController::class, 'showDetail'])->name('ticket.detail');

// Proses Checkout
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/{ticket}/{snapToken}', [CheckoutController::class, 'show'])->name('checkout');

Route::post('/midtrans/notification', [CheckoutController::class, 'notificationHandler'])->name('midtrans.notification');


// Transaksi
Route::get('/transactions', [TransactionController::class, 'index'])->name("transactions");
Route::post('/tiket/bayar', [TicketController::class, 'storeTicket'])->name('ticket.store');

// Halaman Informasi Tambahan
Route::get('/faqs', function () {
    return view('pages.faqs');
})->name('faqs');

Route::get('/privacy-policy', function () {
    return view('pages.privacy-policy');
})->name('privacy-policy');

// Route::get('/live-score', [LiveScoreController::class, 'index'])->name('live-score');
// Route::get('/matches', [MatchController::class, 'index'])->name('matches');
// Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
// Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
// Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
// Route::get('/setting', [SettingController::class, 'index'])->name('setting');
