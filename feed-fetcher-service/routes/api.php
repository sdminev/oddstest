<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedFetcherController;

Route::get('/fetch', [FeedFetcherController::class, 'fetch']);