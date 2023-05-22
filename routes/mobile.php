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

Route::post('login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::post('mobile-login', [\App\Http\Controllers\Mobile\Auth\MobileLoginController::class, 'index']);
Route::post('otp-verification', [\App\Http\Controllers\Mobile\Auth\MobileLoginController::class, 'otpVerification']);


Route::group(['middleware' => ['jwt']], function () {

    //LA USERS
    Route::group(['prefix' => 'la'],function () {

        //FARM AND COW
        Route::post('get-farmList-info',[App\Http\Controllers\Mobile\La\Farm\FarmController::class,'getFarmInfo']);
        Route::post('store-farm-data',[App\Http\Controllers\Mobile\La\Farm\FarmController::class,'storeFarmData']);
        Route::post('store-cow-data',[App\Http\Controllers\Mobile\La\Farm\CowController::class,'storeCowData']);

        //BREEDING AND
        Route::get('get-bull-data',[App\Http\Controllers\Mobile\La\Breeding\BreedingController::class,'getBullData']);
        Route::post('store-breeding-data',[App\Http\Controllers\Mobile\La\Breeding\BreedingController::class,'storeBreedingData']);
        Route::post('get-breeding-data',[App\Http\Controllers\Mobile\La\Breeding\BreedingController::class,'getBreedingData']);

        //RE BREEDING
        Route::post('get-re-breeding-data',[App\Http\Controllers\Mobile\La\Breeding\ReBreedingController::class,'getReBreedingData']);
        Route::post('store-re-breeding-data',[App\Http\Controllers\Mobile\La\Breeding\ReBreedingController::class,'storeReBreedingData']);


    });


});
