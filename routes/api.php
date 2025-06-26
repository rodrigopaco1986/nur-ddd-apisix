<?php

use App\Http\Controllers\Api\PersonalAccessTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [PersonalAccessTokenController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/users', function () {
        return redirect()->away('https://jsonplaceholder.typicode.com/users');
    });

    Route::get('/posts', function () {
        return redirect()->away('https://jsonplaceholder.typicode.com/posts');
    });
});
