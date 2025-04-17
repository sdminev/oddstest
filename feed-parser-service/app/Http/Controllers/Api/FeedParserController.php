<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedParserController extends Controller
{
    public function parse(Request $request)
    {
        $raw = $request->getContent();

        if (!$raw) {
            Log::warning('[FeedParser] Empty payload received.');
            return response()->json(['error' => 'Empty payload'], 400);
        }

        // Try JSON
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            Log::info('[FeedParser] JSON feed parsed successfully.');
            return $this->forwardParsedData($json, 'json');
        }

        // Try XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($raw);
        if ($xml !== false) {
            Log::info('[FeedParser] XML feed parsed successfully.');
            $jsonified = json_decode(json_encode($xml), true);
            return $this->forwardParsedData($jsonified, 'xml');
        }

        Log::error('[FeedParser] Unrecognized feed format.', [
            'payload_snippet' => substr($raw, 0, 200),
        ]);

        return response()->json(['error' => 'Unrecognized feed format'], 422);
    }

    private function forwardParsedData(array $parsed, string $type)
    {
        $response = Http::post('http://feed-transformer:8000/api/transform', $parsed);

        return response()->json([
            'type' => $type,
            'parsed' => $parsed,
            'transformed_response' => $response->json(),
        ]);
    }
}
