<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;

Route::post('/store', [DataController::class, 'store']);