<?php

use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\DeliveryController;
use App\Http\Controllers\Auth\UserController;
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
        Route::post('login',[AdminController::class,'login']);
        Route::group(['middleware'=>['auth:sanctum','abilities:admin']],static function(){
            Route::post('update',[AdminController::class,'update']);
            Route::post('change_password',[AdminController::class,'changePassword']);
            Route::post('logout',[AdminController::class,'logout']);
            Route::post('info',[AdminController::class,'info']);
        });

    });

    Route::group(['prefix'=>'user'],static function(){
        Route::post('register',[UserController::class,'register']);
        Route::post('login',[UserController::class,'login']);
        Route::group(['middleware'=>['auth:sanctum','abilities:user']],static function(){
            Route::post('update',[UserController::class,'update']);
            Route::post('change_password',[UserController::class,'changePassword']);
            Route::post('logout',[UserController::class,'logout']);
            Route::post('info',[UserController::class,'info']);
        });

    });

    Route::group(['prefix'=>'delivery'],static function(){
        Route::post('register',[DeliveryController::class,'register']);
        Route::post('login',[DeliveryController::class,'login']);
        Route::group(['middleware'=>['auth:sanctum','abilities:delivery']],static function(){
            Route::post('update',[DeliveryController::class,'update']);
            Route::post('change_password',[DeliveryController::class,'changePassword']);
            Route::post('logout',[DeliveryController::class,'logout']);
            Route::post('info',[DeliveryController::class,'info']);
        });
    });

});


