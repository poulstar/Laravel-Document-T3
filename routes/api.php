<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Enum\Permissions;

Route::get('get-verify-code/{phone}', [UserController::class, 'getLoginCode']);
Route::post('register', [UserController::class, 'register']);

Route::Group([
    'middleware' => ['auth:api']
], function () {
    Route::get('user/profile', [UserController::class, 'profile'])->middleware(['can:' . Permissions::VIEW_MY_PROFILE]);
    Route::post('update-my-profile/{user}', [UserController::class, 'updateMyProfile'])->middleware(['can:' . Permissions::UPDATE_MY_ACCOUNT]);
});
