<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FeedController extends Controller
{
    public function fetch()
    {
        $url = config('feed.source_url');
        $response = Http::get($url);
        return response($response->body(), $response->status())
                 ->header('Content-Type', 'application/xml');
    }
}
