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


Route::group(['middleware' => ['jwt']], function () {
    Route::post('logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
    Route::post('refresh', [\App\Http\Controllers\Auth\AuthController::class, 'refresh']);
    Route::post('me', [\App\Http\Controllers\Auth\AuthController::class, 'me']);
    Route::get('app-supporting-data', [\App\Http\Controllers\Common\HelperController::class, 'appSupportingData']);
});


Route::group(['middleware' => ['jwt:api']], function () {

    // ADMIN USERS
    Route::group(['prefix' => 'user'],function () {
        Route::post('list', [\App\Http\Controllers\Admin\Users\AdminUserController::class, 'index']);
        //User Modal Data
        Route::get('modal',[\App\Http\Controllers\Admin\Users\AdminUserController::class,'userModalData']);
        Route::post('add', [\App\Http\Controllers\Admin\Users\AdminUserController::class, 'store']);
        Route::get('get-user-info/{UserID}',[\App\Http\Controllers\Admin\Users\AdminUserController::class,'getUserInfo']);
        Route::post('update', [\App\Http\Controllers\Admin\Users\AdminUserController::class, 'update']);
        Route::post('password-change',[\App\Http\Controllers\Common\HelperController::class,'passwordChange']);
    });

    // ADMIN ACTION
    Route::group(['prefix' => 'admin'],function () {

        //Farms
        Route::post('farmList', [\App\Http\Controllers\Admin\Farm\FarmController::class, 'index']);
        Route::post('add-farm-list-data', [\App\Http\Controllers\Admin\Farm\FarmController::class, 'store']);
        Route::get('get-farmList-info/{FarmID}',[\App\Http\Controllers\Admin\Farm\FarmController::class,'getFarmInfo']);
        Route::post('update-farm-list-data', [\App\Http\Controllers\Admin\Farm\FarmController::class, 'updateFarmData']);

        //Breeding
        Route::post('BreedingList', [\App\Http\Controllers\Admin\Farm\FarmController::class, 'index']);
        Route::post('add-breeding-list-data', [\App\Http\Controllers\Admin\Farm\FarmController::class, 'store']);
        Route::get('get-breedingList-info/{EntryID}',[\App\Http\Controllers\Admin\Farm\FarmController::class,'getBreedingInfo']);
        Route::post('update-breeding-list-data', [\App\Http\Controllers\Admin\Farm\FarmController::class, 'updateBreedingData']);

    });


});

