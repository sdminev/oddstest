<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;

Route::get('/api/fetch', [FeedController::class, 'fetch']);
