<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedTransformerController extends Controller
{
    public function transform(Request $request)
    {
        $data = $request->all();

        $normalized = [
            'match_id' => $data['match_id'] ?? null,
            'bookmaker' => $data['bookmaker'] ?? 'Unknown',
            'home_team' => $data['team'] ?? 'Unknown',
            'away_team' => 'TBD',
            'odds' => $data['odds'] ?? null,
        ];

        $processorUrl = config('services.processor.url') . '/api/store';

        try {
            $processorResponse = Http::timeout(10)->post($processorUrl, $normalized);

            if (!$processorResponse->successful()) {
                Log::error('Failed to forward data to processor', [
                    'status' => $processorResponse->status(),
                    'body' => $processorResponse->body(),
                ]);

                return response()->json([
                    'error' => 'Failed to store normalized data',
                ], $processorResponse->status());
            }

            return response()->json([
                'type' => 'json',
                'parsed' => $data,
                'transformed_response' => [
                    'normalized' => $normalized,
                    'received' => $data,
                ],
                'processor_response' => $processorResponse->json(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception while contacting processor', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Exception while storing data to processor',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}