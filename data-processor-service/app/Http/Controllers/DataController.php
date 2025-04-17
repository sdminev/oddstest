<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatchData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'match_id' => 'required|integer',
            'bookmaker' => 'required|string',
            'home_team' => 'required|string',
            'away_team' => 'nullable|string',
            'odds' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for incoming data', [
                'errors' => $validator->errors()->toArray(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors(),
            ], 422);
        }

        try {
            $match = MatchData::create($validator->validated());

             // 🔔 Emit the broadcast event
            event(new \App\Events\MatchDataStored($match));
            $match = MatchData::create($validator->validated());

            Log::info('Match data successfully stored', [
                'match_id' => $match->id,
                'source' => 'transformer',
            ]);

            return response()->json([
                'message' => 'Match data stored successfully',
                'data' => $match,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to store match data', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}