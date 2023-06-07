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
    Route::get('dashboard-data',[\App\Http\Controllers\Common\DashboardController::class,'index']);

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

        //Breeding(Entries)
        Route::post('BreedingList', [\App\Http\Controllers\Admin\Breeding\BreedingController::class, 'index']);
        Route::post('add-breeding-list-data', [\App\Http\Controllers\Admin\Breeding\BreedingController::class, 'store']);
        Route::get('get-breedingList-info/{EntryID}',[\App\Http\Controllers\Admin\Breeding\BreedingController::class,'getBreedingInfo']);
        Route::post('update-breeding-list-data', [\App\Http\Controllers\Admin\Breeding\BreedingController::class, 'updateBreedingData']);

        //Breeding(BullType Id)
        Route::post('breeding/bullTypeList', [\App\Http\Controllers\Admin\Breeding\BullTypeController::class, 'index']);
        Route::post('breeding/add-bull-type-list-data', [\App\Http\Controllers\Admin\Breeding\BullTypeController::class, 'store']);
        Route::get('breeding/get-bull-type-info/{BullTypeID}',[\App\Http\Controllers\Admin\Breeding\BullTypeController::class,'getBullTypeInfo']);
        Route::post('update-bull-type-list-data', [\App\Http\Controllers\Admin\Breeding\BullTypeController::class, 'updateBullTypeData']);

        //Breeding(Bull)
        Route::post('breeding/bullList', [\App\Http\Controllers\Admin\Breeding\BullController::class, 'index']);
        Route::get('bull/modal',[\App\Http\Controllers\Admin\Breeding\BullController::class,'bullModalData']);
        Route::post('breeding/add-bull-list-data', [\App\Http\Controllers\Admin\Breeding\BullController::class, 'store']);
        Route::get('breeding/get-bull-info/{BullID}',[\App\Http\Controllers\Admin\Breeding\BullController::class,'getBullInfo']);
        Route::post('update-bull-list-data', [\App\Http\Controllers\Admin\Breeding\BullController::class, 'updateBullData']);

        //Setting
        //Event
        Route::post('setting/eventList', [\App\Http\Controllers\Admin\Setting\Event\EventController::class, 'index']);
        Route::post('setting/eventList/add-event-list-data', [\App\Http\Controllers\Admin\Setting\Event\EventController::class,'store']);
        Route::get('setting/eventList/get-event-list-info/{EventID}', [\App\Http\Controllers\Admin\Setting\Event\EventController::class,'getEventInfo']);
        Route::post('update/setting/eventList/add-event-list-data', [\App\Http\Controllers\Admin\Setting\Event\EventController::class,'updateEventData']);

        //Notice
        Route::post('setting/noticeList', [\App\Http\Controllers\Admin\Setting\Notice\NoticeController::class, 'index']);
        Route::post('setting/noticeList/add-notice-list-data', [\App\Http\Controllers\Admin\Setting\Notice\NoticeController::class,'store']);
        Route::get('setting/noticeList/get-notice-list-info/{NoticeID}', [\App\Http\Controllers\Admin\Setting\Notice\NoticeController::class,'getNoticeInfo']);
        Route::post('update/setting/noticeList/add-notice-list-data', [\App\Http\Controllers\Admin\Setting\Notice\NoticeController::class,'updateNoticeData']);

        //Report
        Route::post('report/breedingReportList', [\App\Http\Controllers\Admin\Report\BreedingReportController::class, 'index']);
        Route::post('report/reBreedingReportList', [\App\Http\Controllers\Admin\Report\BreedingReportController::class, 'reBreeding']);
        Route::post('report/pregnancy', [\App\Http\Controllers\Admin\Report\PregnancyReportController::class, 'index']);
        Route::post('report/culf', [\App\Http\Controllers\Admin\Report\CulfReportController::class, 'index']);



    });


});

