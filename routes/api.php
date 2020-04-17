<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('/decode', 'DecodingController@DecodeFile');



Route::get('/encode', 'EncodingController@EncodeString');



Route::any('/{any}', function () {
    
    response()->json([
        'status'    => 'error',
        'message'   => 'route not found'
    ], 404 )->send();

})->where('any', '.*');

