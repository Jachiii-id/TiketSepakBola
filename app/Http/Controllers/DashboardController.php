<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scrapping;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function view()
    {
        // Ambil semua data dari tabel scrapping
        $scrappingData = Scrapping::all();

        // Hitung total like, komentar, dan jumlah kota unik
        $totalLikes = $scrappingData->sum('like_count');
        $totalComments = $scrappingData->sum('comment_count');
        $uniqueLocations = $scrappingData->pluck('location')->unique()->count();

        $postTypeCounts = [
            'image' => DB::table('scrapping')->where('is_video', 0)->count(),
            'video' => DB::table('scrapping')->where('is_video', 1)->count(),
        ];

        // dd($postTypeCounts);

         $hashtagData = Scrapping::select('hashtag', DB::raw('SUM(like_count) as total_likes'), DB::raw('SUM(comment_count) as total_comments'))
         ->groupBy('hashtag')
         ->get();

        $hashtags = $hashtagData->pluck('hashtag');
        $likes = $hashtagData->pluck('total_likes');
        $comments = $hashtagData->pluck('total_comments');

        $locations = DB::table('scrapping')
        ->select(
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.city')) as city"),
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.lat')) as lat"),
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.lng')) as lng")
        )
        ->get();

        $lists = DB::table('scrapping')
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.city')) as city"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.lat')) as lat"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.lng')) as lng"),
                'hashtag', 
                'post_url', 
                'like_count', 
                'comment_count', 
                'thumbnail_url', 
                'is_video', 
                'scrappingId'
            )
            ->orderBy('hashtag', 'asc') // Perbaikan penggunaan orderBy
            ->paginate(10); // Menambahkan paginate untuk menampilkan 10 data per halaman


        // dd($hashtags, $likes, $comments);

        return view('pages.dashboard-tactick', compact('scrappingData', 'totalLikes', 'totalComments', 'uniqueLocations', 'postTypeCounts', 'hashtags', 'likes', 'comments', 'locations', 'lists'));
    }
}
