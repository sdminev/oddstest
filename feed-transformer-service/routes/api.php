<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FeedTransformerController;

Route::post('/transform', [FeedTransformerController::class, 'transform']);