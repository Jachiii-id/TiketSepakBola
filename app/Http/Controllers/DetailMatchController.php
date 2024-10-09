<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Matches;

class DetailMatchController extends Controller
{
    public function showDetailMatches($id)
    {
        // Fetch data from the matches table
        $matches = Ticket::with('getClub1')->get()->where('id', $id);

        $match = Matches::findOrFail($id);

        // Add the breadcrumbs
        $breadcrumbs = [
            ['name' => 'Beranda', 'url' => route('index')],
            // ['name' => 'Match Detail', 'url' => route('match-detail', ['id' => $match->id])],
            ['name' => $match->name, 'url' => route('match-detail', ['id' => $match->id])],
        ];

        // Pass the data to the view
        return view('pages.matches-detail', compact('matches', 'breadcrumbs'));
    }
}
