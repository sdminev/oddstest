<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FeedParserController;

Route::post('/parse', [FeedParserController::class, 'parse']);