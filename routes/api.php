<?php

use App\Http\Controllers\Api\GiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('gifts', [GiftController::class, 'index']);
});
