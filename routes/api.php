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
//
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware'=>['api','password']],static function(){

    Route::group(['prefix'=>'admin'],static function(){
        Route::post('register',[AdminController::class,'register']);
    });

    Route::group(['prefix'=>'user'],static function(){

    });

    Route::group(['prefix'=>'delivery'],static function(){

    });

});


