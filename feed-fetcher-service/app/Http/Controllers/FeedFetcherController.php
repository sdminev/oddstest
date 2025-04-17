<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FeedFetcherController extends Controller
{
    public function fetch()
    {
        $url = config('feed.source_url');
        $response = Http::get($url);

        if ($response->successful()) {
            // You can return XML or JSON here for now
            return response($response->body(), 200)
                ->header('Content-Type', $response->header('Content-Type', 'text/plain'));
        }

        return response()->json(['error' => 'Failed to fetch feed'], 500);
    }
}
