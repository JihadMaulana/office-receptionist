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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('divisions', 'App\Http\Controllers\Api\DivisionController', [ 'as' => 'api' ]);
Route::apiResource('guest_requests', 'App\Http\Controllers\Api\GuestRequestController', [ 'as' => 'api' ]);
Route::get('search_employee', 'App\Http\Controllers\Api\SearchEmployeeController')->name('api.search_employee');
Route::post('request_meet', 'App\Http\Controllers\RequestMeetController')->name('request_meet');
