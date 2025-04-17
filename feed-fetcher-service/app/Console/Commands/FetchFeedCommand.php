<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchFeedCommand extends Command
{
    protected $signature = 'feed:fetch';
    protected $description = 'Fetches the remote feed and forwards it to the parser service';

    public function handle(): void
    {
        $url = config('feed.source_url');
        $parserUrl = 'http://feed-parser:8000/api/parse';

        if (!$url) {
            Log::error('[FeedFetcher] No FEED_URL set in environment.');
            $this->error('No feed URL set.');
            return;
        }

        try {
            retry(3, function ($attempt) use ($url, $parserUrl) {
                $this->info("[Attempt {$attempt}] Fetching feed from {$url}");
                Log::info("[FeedFetcher] Attempt {$attempt}: Fetching feed", ['url' => $url]);

                $response = Http::timeout(10)->get($url);

                if (!$response->successful()) {
                    throw new \Exception("Feed request failed: HTTP " . $response->status());
                }

                $feedData = $response->body();
                $this->info("Feed fetched successfully.");
                Log::info("[FeedFetcher] Feed fetched successfully.", [
                    'status' => $response->status(),
                    'body_snippet' => substr($feedData, 0, 300),
                ]);

                // Detect JSON vs XML
                $contentType = str_starts_with(trim($feedData), '<') ? 'application/xml' : 'application/json';

                $forwardResponse = Http::withHeaders([
                    'Content-Type' => $contentType,
                ])->timeout(10)->withBody($feedData, $contentType)->post($parserUrl);

                if (!$forwardResponse->successful()) {
                    throw new \Exception("Parser response failed: HTTP " . $forwardResponse->status());
                }

                $this->info("Feed successfully forwarded to parser.");
                Log::info("[FeedFetcher] Feed forwarded to parser", [
                    'parser_url' => $parserUrl,
                    'parser_status' => $forwardResponse->status(),
                    'parser_response_snippet' => substr($forwardResponse->body(), 0, 300),
                ]);
            }, function ($attempt) {
                return $attempt * 2000; // 2s, 4s, 6s
            });
        } catch (\Throwable $e) {
            Log::error('[FeedFetcher] Feed fetch or forwarding failed', [
                'url' => $url,
                'parser_url' => $parserUrl,
                'error' => $e->getMessage(),
            ]);
            $this->error('[FeedFetcher] Operation failed: ' . $e->getMessage());
        }
    }
}