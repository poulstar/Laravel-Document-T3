<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('get-verify-code/{phone}', [UserController::class, 'getLoginCode']);

